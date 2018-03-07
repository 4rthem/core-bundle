/* jshint nomen:false */
/* global google, window */
exports.ArthemGoogleAutoComplete = function (
    autocompleteFieldId,
    formattedAddressId,
    streetId,
    postalCodeId,
    cityId,
    regionId,
    countryId,
    latitudeId,
    longitudeId
) {
    let autocomplete;
    const componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'short_name',
        postal_code: 'short_name'
    };

    const extended = autocompleteFieldId !== formattedAddressId;

    const $input = document.getElementById(autocompleteFieldId);
    autocomplete = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */($input),
        {types: ['geocode']});

    google.maps.event.addDomListener($input, 'focus', function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const geolocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                const circle = new google.maps.Circle({
                    center: geolocation,
                    radius: position.coords.accuracy
                });
                autocomplete.setBounds(circle.getBounds());
            });
        }
    });

    function reset() {
        let formFields = [
            streetId,
            postalCodeId,
            cityId,
            regionId,
            countryId,
            latitudeId,
            longitudeId,
        ];
        for (let f in formFields) {
            if (formFields.hasOwnProperty(f)) {
                document.getElementById(formFields[f]).value = '';
            }
        }
    }

    function isValid() {
        return document.getElementById(countryId).value;
    }

    let lastValue = $input.value.trim();
    google.maps.event.addDomListener($input, 'keydown', function (event) {
        if (event.keyCode === 13) {
            event.preventDefault();
        }
    });
    google.maps.event.addDomListener($input, 'keyup', function () {
        if (lastValue !== $input.value.trim()) {
            if (!extended) {
                reset();
            }
            lastValue = $input.value.trim();
        }
    });

    let lastFocusValue = $input.value.trim();
    google.maps.event.addDomListener($input, 'blur', function () {
        if (this.value.trim() !== lastFocusValue) {
            if (!isValid()) {
                $input.value = '';
            }
            lastFocusValue = $input.value.trim();
        }
    });

    // When the user selects an address from the dropdown, populate the address
    // fields in the form.
    autocomplete.addListener('place_changed', fillInAddress);

    function fillInAddress() {
        // Get the place details from the autocomplete object.
        const place = autocomplete.getPlace();

        if (!place.address_components) {
            return;
        }

        let d = {};
        for (let i = 0; i < place.address_components.length; i++) {
            let addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                d[addressType] = place.address_components[i][componentForm[addressType]];
            }
        }

        const $street = document.getElementById(streetId);
        $street.value = d.street_number ? d.street_number : '';
        if (d.street_number && d.route) {
            $street.value += ' ';
        }
        if (d.route) {
            $street.value += d.route;
        }

        if (extended) {
            document.getElementById(formattedAddressId).value = place.formatted_address;
        }
        document.getElementById(postalCodeId).value = d.postal_code || '';
        document.getElementById(cityId).value = d.locality || '';
        document.getElementById(regionId).value = d.administrative_area_level_1 || '';
        document.getElementById(countryId).value = d.country || '';
        document.getElementById(latitudeId).value = place.geometry.location.lat() || '';
        document.getElementById(longitudeId).value = place.geometry.location.lng() || '';
    }
};
