/**
 * Sync Trove metabox range sliders with paired number inputs (show_number).
 */
(function () {
	function clamp(v, min, max) {
		v = parseInt(v, 10);
		if (Number.isNaN(v)) {
			return min;
		}
		return Math.min(Math.max(v, min), max);
	}

	function bindRow(wrap) {
		var range = wrap.querySelector('.trove-metabox-range');
		var num = wrap.querySelector('.trove-metabox-field__range-number');
		if (!range || !num) {
			return;
		}
		var min = parseInt(range.getAttribute('min'), 10);
		if (Number.isNaN(min)) {
			min = 0;
		}
		var max = parseInt(range.getAttribute('max'), 10);
		if (Number.isNaN(max)) {
			max = 999;
		}

		range.addEventListener('input', function () {
			num.value = range.value;
		});

		num.addEventListener('input', function () {
			var v = clamp(num.value, min, max);
			num.value = String(v);
			range.value = String(v);
		});
	}

	document.querySelectorAll('.trove-metabox-field--range').forEach(bindRow);
})();
