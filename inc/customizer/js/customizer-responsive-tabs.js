/**
 * Trove Customizer: responsive tab control — single input, switch active theme_mod by device.
 */
(function ($, api) {
	"use strict";

	/**
	 * Trove option keys (pc/tablet/phone) -> Customizer preview frame keys (desktop/tablet/mobile).
	 *
	 * @see WP_Customize_Manager::get_previewable_devices()
	 */
	function troveDeviceToPreviewDevice(trove) {
		var map = { pc: "desktop", tablet: "tablet", phone: "mobile" };
		return map[trove] || null;
	}

	function setCustomizerPreviewDevice(trove) {
		var previewKey = troveDeviceToPreviewDevice(trove);
		if (
			!previewKey ||
			!api.previewedDevice ||
			typeof api.previewedDevice.get !== "function" ||
			typeof api.previewedDevice.set !== "function"
		) {
			return;
		}
		var list = api.settings && api.settings.previewableDevices;
		if (list && !Object.prototype.hasOwnProperty.call(list, previewKey)) {
			return;
		}
		try {
			if (api.previewedDevice.get() !== previewKey) {
				api.previewedDevice.set(previewKey);
			}
		} catch (err) {
			// Preview state may be unavailable in edge contexts.
		}
	}

	function parseSettings($root) {
		var parsed = $root.data("settings");
		if (parsed && typeof parsed === "object") {
			return parsed;
		}
		var raw = $root.attr("data-settings");
		if (!raw) {
			return null;
		}
		try {
			return JSON.parse(raw);
		} catch (e) {
			return null;
		}
	}

	function initOne($root) {
		var fieldType = $root.data("field-type") || "text";
		var settingsMap = parseSettings($root);
		if (!settingsMap || !settingsMap.pc || !settingsMap.tablet || !settingsMap.phone) {
			return;
		}

		var $input = $root.find(".trove-r-active-input");
		var $tabs = $root.find(".trove-r-tab");
		var active = "pc";
		var colorPickerInited = false;
		var settings = {};
		var remaining = 3;

		function readInputValue() {
			if (fieldType === "checkbox") {
				return $input.prop("checked");
			}
			if (fieldType === "number") {
				var n = parseFloat($input.val(), 10);
				return isNaN(n) ? 0 : n;
			}
			return $input.val();
		}

		function writeInputValue(v) {
			if (fieldType === "checkbox") {
				$input.prop("checked", !!v);
				return;
			}
			if (v === null || typeof v === "undefined") {
				v = "";
			}
			$input.val(v);
			if (fieldType === "color" && colorPickerInited && $.fn.wpColorPicker) {
				try {
					$input.wpColorPicker("color", v || "");
				} catch (err) {
					// Iris may throw if value empty.
				}
			}
		}

		function getSetting(device) {
			return settings[device] || null;
		}

		function commit() {
			var s = getSetting(active);
			if (!s) {
				return;
			}
			s.set(readInputValue());
		}

		function load(device) {
			var s = getSetting(device);
			if (!s) {
				return;
			}
			writeInputValue(s.get());
		}

		function activate(device) {
			if (device === active) {
				return;
			}
			commit();
			active = device;
			$tabs.removeClass("is-active").attr("aria-selected", "false");
			$tabs.filter('[data-device="' + device + '"]').addClass("is-active").attr("aria-selected", "true");
			load(device);
			setCustomizerPreviewDevice(device);
		}

		function initColorPicker() {
			if (fieldType !== "color" || colorPickerInited || !$input.length || !$.fn.wpColorPicker) {
				return;
			}
			colorPickerInited = true;
			$input.wpColorPicker({
				change: function (event, ui) {
					if (ui && ui.color) {
						var s = getSetting(active);
						if (s) {
							s.set(ui.color.toString());
						}
					}
				},
				clear: function () {
					var s = getSetting(active);
					if (s) {
						s.set("");
					}
				},
			});
		}

		function wireUi() {
			$tabs.on("click", function (e) {
				e.preventDefault();
				var d = $(this).data("device");
				if (d) {
					activate(d);
				}
			});

			if (fieldType === "checkbox") {
				$input.on("change", function () {
					var s = getSetting(active);
					if (s) {
						s.set($input.prop("checked"));
					}
				});
			} else if (fieldType === "select") {
				$input.on("change", function () {
					var s = getSetting(active);
					if (s) {
						s.set($input.val());
					}
				});
			} else if (fieldType === "number") {
				$input.on("change input", function () {
					var s = getSetting(active);
					if (s) {
						s.set(readInputValue());
					}
				});
			} else if (fieldType === "text") {
				$input.on("input change", function () {
					var s = getSetting(active);
					if (s) {
						s.set($input.val());
					}
				});
			} else if (fieldType === "color") {
				$input.on("change", function () {
					var s = getSetting(active);
					if (s) {
						s.set($input.val());
					}
				});
			}

			["pc", "tablet", "phone"].forEach(function (dev) {
				var s = getSetting(dev);
				if (!s) {
					return;
				}
				s.bind(function () {
					if (dev === active) {
						load(dev);
					}
				});
			});

			load("pc");
			if (fieldType === "color") {
				initColorPicker();
			}
		}

		["pc", "tablet", "phone"].forEach(function (dev) {
			api(
				settingsMap[dev],
				function (setting) {
					settings[dev] = setting;
					remaining -= 1;
					if (remaining === 0) {
						wireUi();
					}
				}
			);
		});
	}

	$(function () {
		$(".trove-responsive-tabs-control").each(function () {
			initOne($(this));
		});
	});
})(jQuery, wp.customize);
