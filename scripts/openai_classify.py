#!/usr/bin/env python3
"""
Read a JSON file exported by the Symfony command (array of {id, commentaire})
and classify each comment with OpenAI (returns Positive/Neutral/Negative).

Usage:
    python3 scripts/openai_classify.py --input var/avis_sample.json --output var/avis_classified.json

Set environment variable OPENAI_API_KEY before running.
"""

import os
import json
import time
import argparse

try:
    import openai
except ImportError:
    raise SystemExit("Please install the OpenAI python package: pip install openai")


PROMPT_TEMPLATE = (
    "Classify the sentiment of the following user review into exactly one of these labels: Positive, Neutral, Negative."
    "\nReturn ONLY the label (one word) as the answer.\n\nReview:\n\"{text}\"\n"
)


def classify_single(client, text, model="gpt-3.5-turbo", retries=2):
    prompt = PROMPT_TEMPLATE.format(text=text.replace('"','\\"'))
    messages = [{"role": "user", "content": prompt}]
    for attempt in range(retries + 1):
        try:
            resp = client.chat.completions.create(model=model, messages=messages, max_tokens=6)
            # Newer OpenAI SDKs differ; adjust if necessary
            content = resp.choices[0].message.content.strip()
            # keep only the first word that matches label
            for token in content.split():
                t = token.strip().strip('.,!"\'')
                if t.lower().startswith('p'):
                    return 'Positive'
                if t.lower().startswith('n'):
                    # could be Neutral or Negative; check second letter
                    if t.lower().startswith('neut'):
                        return 'Neutral'
                    return 'Negative'
            # fallback: if contains positive/negative words
            low = content.lower()
            if 'positive' in low:
                return 'Positive'
            if 'neutral' in low:
                return 'Neutral'
            if 'negative' in low:
                return 'Negative'
            return 'Neutral'
        except Exception as e:
            if attempt < retries:
                time.sleep(1 + attempt * 2)
                continue
            raise


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--input', '-i', required=True, help='Input JSON exported by Symfony (array of {id, commentaire})')
    parser.add_argument('--output', '-o', required=True, help='Output JSON file with sentiment labels')
    parser.add_argument('--model', default='gpt-3.5-turbo', help='OpenAI model to use')
    args = parser.parse_args()

    api_key = os.getenv('OPENAI_API_KEY')
    if not api_key:
        raise SystemExit('Please set OPENAI_API_KEY environment variable')

    openai.api_key = api_key
    client = openai

    with open(args.input, 'r', encoding='utf-8') as f:
        data = json.load(f)

    results = []
    for item in data:
        cid = item.get('id')
        text = item.get('commentaire') or ''
        if not text.strip():
            label = 'Neutral'
        else:
            label = classify_single(client, text, model=args.model)
        results.append({'id': cid, 'commentaire': text, 'sentiment': label})
        print(f'[{cid}] -> {label}')
        time.sleep(0.35)  # be gentle with rate limits

    with open(args.output, 'w', encoding='utf-8') as f:
        json.dump(results, f, ensure_ascii=False, indent=2)

    print('Done. Output:', args.output)


if __name__ == '__main__':
    main()
