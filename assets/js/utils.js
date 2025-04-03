function incrementValue(fieldId, minValue = 0, validateCallback = null, additionalCallback = null) {
    const input = document.getElementById(fieldId);
    let value = parseInt(input.value) || minValue;
    input.value = value + 1;
    if (validateCallback && !validateCallback(input.value, fieldId.includes('Stock') ? 'Stock' : 'Cantidad')) {
        input.value = value; 
        return;
    }
    if (additionalCallback) additionalCallback();
}

function decrementValue(fieldId, minValue = 0, validateCallback = null, additionalCallback = null) {
    const input = document.getElementById(fieldId);
    let value = parseInt(input.value) || minValue;
    if (value > minValue) {
        input.value = value - 1;
        if (validateCallback && !validateCallback(input.value, fieldId.includes('Stock') ? 'Stock' : 'Cantidad')) {
            input.value = value;
            return;
        }
        if (additionalCallback) additionalCallback();
    }
}

function validateQuantity(value, fieldName) {
    if (!Number.isInteger(parseFloat(value))) {
        Swal.fire('Error', `El campo ${fieldName} no puede contener decimales.`, 'error');
        return false;
    }
    return true;
}