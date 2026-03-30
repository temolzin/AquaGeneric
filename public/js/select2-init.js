$(document).ready(function() {
    initializeSelect2();
    $(document).on('shown.bs.modal', '.modal', function() {
        initializeSelect2InModal($(this));
    });

    $(document).on('click', '.select2-dropdown', function(e) {
        e.stopPropagation();
    });
});

function initializeSelect2() {
    $('.select2').each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
            return;
        }
        
        let config = {
            allowClear: false,
            placeholder: 'Selecciona una opción',
            width: '100%'
        };

        let modal = $(this).closest('.modal');
        if (modal.length) {
            config.dropdownParent = modal;
        }
        
        $(this).select2(config);
    });
}

/**
 * Initialize Select2 for elements inside a specific modal
 * @param {jQuery} modalElement
 */
function initializeSelect2InModal(modalElement) {
    modalElement.find('.select2').each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
        
        let config = {
            allowClear: false,
            placeholder: 'Selecciona una opción',
            width: '100%',
            dropdownParent: modalElement
        };
        
        $(this).select2(config);
    });

    modalElement.find('.select2-search__field').on('keydown', function(e) {
        if (e.which === 27) {
            e.stopPropagation();
        }
    });
}
function reinitializeSelect2() {
    $('.select2').each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
    });
    
    initializeSelect2();
}

/**
 * Initialize a specific select2 element with custom configuration
 * @param {string} selector
 * @param {object} customConfig
 */
function initializeSelect2Custom(selector, customConfig = {}) {
    let $element = $(selector);
    
    let config = {
        allowClear: false,
        placeholder: 'Selecciona una opción',
        width: '100%',
        ...customConfig
    };

    if (!config.dropdownParent) {
        let $modal = $element.closest('.modal');
        if ($modal.length) {
            config.dropdownParent = $modal;
        }
    }
    
    $element.select2(config);
}
