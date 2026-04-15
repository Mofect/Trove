/**
 * Trove Customizer: section tabs (General / Design / …) — show only controls for the active tab.
 */
(function ($, api) {
	"use strict";

	function applyTabVisibility(sectionId, activeTab, map) {
		var $sub = $("#sub-accordion-section-" + sectionId);
		if (!$sub.length || !map) {
			return;
		}
		$sub.find("> li.customize-control").each(function () {
			var $li = $(this);
			if ($li.find(".trove-cz-section-tabs").length) {
				$li.removeClass("trove-cz-tab-hidden");
				return;
			}
			var id = $li.attr("id") || "";
			var prefix = "customize-control-";
			if (id.indexOf(prefix) !== 0) {
				return;
			}
			var controlId = id.slice(prefix.length);
			if (map[controlId] === undefined) {
				return;
			}
			if (map[controlId] === activeTab) {
				$li.removeClass("trove-cz-tab-hidden");
			} else {
				$li.addClass("trove-cz-tab-hidden");
			}
		});
	}

	function wireSection(sectionId, config) {
		var map = config.controls || {};
		var settingId = config.setting;
		var $sub = $("#sub-accordion-section-" + sectionId);
		if (!$sub.length) {
			return;
		}

		api(settingId, function (setting) {
			var $tabs = $sub.find(".trove-cz-section-tabs").first();
			var $hidden = $tabs.length
				? $tabs.closest("li").find('input[type="hidden"][data-customize-setting-link]')
				: $();

			function refresh() {
				applyTabVisibility(sectionId, setting.get(), map);
			}

			$tabs.find(".trove-cz-section-tab").on("click", function (e) {
				e.preventDefault();
				var tab = $(this).attr("data-trove-section-tab");
				if (tab) {
					setting.set(tab);
				}
			});

			setting.bind(function (newVal) {
				var v = newVal;
				$tabs.find(".trove-cz-section-tab").each(function () {
					var $b = $(this);
					var on = $b.attr("data-trove-section-tab") === v;
					$b.toggleClass("is-active", on).attr("aria-selected", on ? "true" : "false");
				});
				if ($hidden.length) {
					$hidden.val(v);
				}
				refresh();
			});

			refresh();
		});
	}

	api.bind("ready", function () {
		var data = window.troveCzSectionTabs;
		if (!data || !data.sections) {
			return;
		}
		$.each(data.sections, function (sectionId, cfg) {
			wireSection(sectionId, cfg);
		});
	});
})(jQuery, wp.customize);
