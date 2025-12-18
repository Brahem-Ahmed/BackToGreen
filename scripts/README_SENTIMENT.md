Sentiment classification (OpenAI)

1) Export sample from Symfony (run from project root):

```powershell
php bin/console app:avis:export-sample 200
# or specify output: php bin/console app:avis:export-sample 200 --output=var/avis_sample.json
```

This creates `var/avis_sample.json` with an array of objects: `{id, commentaire}`.

2) Install Python dependencies (recommended in a venv):

```bash
python3 -m venv .venv
source .venv/bin/activate    # or `.venv\Scripts\activate` on Windows
pip install openai
```

3) Set your OpenAI API key (example):

```powershell
$env:OPENAI_API_KEY = "sk-..."
```

4) Run the classifier script:

```bash
python3 scripts/openai_classify.py --input var/avis_sample.json --output var/avis_classified.json
```

Output is `var/avis_classified.json` with objects: `{id, commentaire, sentiment}` where sentiment ∈ {Positive, Neutral, Negative}.

Notes:
- The script currently calls the API per comment (simple but slower). We can batch via a single prompt to classify many at once if you prefer.
- After verification, next step is to add a `sentiment` field to `Avis` and persist labels into the DB via a Symfony command.
