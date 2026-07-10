(function () {
    'use strict';

    document.addEventListener('click', function (event) {
        var addBtn = event.target.closest('.jankx-travel-add-row');
        if (addBtn) {
            var targetId = addBtn.getAttribute('data-target');
            var templateId = 'jankx-travel-template-' + addBtn.getAttribute('data-template');
            var wrapper = document.getElementById(targetId);
            var template = document.getElementById(templateId);
            if (wrapper && template) {
                var rows = wrapper.querySelector('.jankx-travel-repeater-rows');
                var clone = template.content.cloneNode(true);
                rows.appendChild(clone);
            }
            return;
        }

        var removeBtn = event.target.closest('.jankx-travel-remove-row');
        if (removeBtn) {
            var row = removeBtn.closest('.jankx-travel-repeater-row');
            var rowsWrap = row ? row.parentElement : null;
            if (row && rowsWrap && rowsWrap.children.length > 1) {
                row.remove();
            } else if (row) {
                // Keep at least one row, just clear the inputs
                row.querySelectorAll('input, textarea').forEach(function (el) {
                    el.value = '';
                });
            }
        }
    });
})();
