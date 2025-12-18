import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        lat: Number,
        lng: Number
    }
    
    connect() {
        // Initialize map when controller connects
        this.initializeMap();
    }
    
    initializeMap() {
        // Map initialization logic
        // Set up click handler to update form fields
    }
    
    updateFormFields(lat, lng) {
        // Update your latitude and longitude form inputs
        const latInput = document.querySelector('input[name*="latitude"]');
        const lngInput = document.querySelector('input[name*="longitude"]');
        
        if (latInput && lngInput) {
            latInput.value = lat;
            lngInput.value = lng;
            // Trigger events for validation
            latInput.dispatchEvent(new Event('change'));
            lngInput.dispatchEvent(new Event('change'));
        }
    }
}