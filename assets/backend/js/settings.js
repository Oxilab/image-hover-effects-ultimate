jQuery.noConflict();
(function ($) {
    $(document).ready(function () {
        var $root = $('.oxi-iheu-settings');
        if (!$root.length) {
            return;
        }
        var timers = {};

        function setStatus(name, state) {
            var $status = $root.find('[data-status-for="' + name + '"]');
            clearTimeout(timers[name]);
            $status.attr('data-state', state).text(state ? $root.attr('data-' + state) : '');
            if (state === 'saved') {
                timers[name] = setTimeout(function () {
                    $status.attr('data-state', '').text('');
                }, 2500);
            }
        }

        // Each option has its own post_{name}() handler in ImageApi, which
        // answers with an oxi-confirmation-success span when it saved.
        function save(name, data, onError) {
            setStatus(name, 'saving');
            $.ajax({
                url: image_hover_settings.ajaxurl,
                method: 'POST',
                data: {
                    action: 'image_hover_settings',
                    _wpnonce: image_hover_settings.nonce,
                    functionname: name,
                    styleid: '',
                    childid: '',
                    rawdata: JSON.stringify(data)
                }
            }).done(function (result) {
                if (String(result).indexOf('oxi-confirmation-success') !== -1) {
                    setStatus(name, 'saved');
                } else {
                    setStatus(name, 'error');
                    if (onError) {
                        onError();
                    }
                }
            }).fail(function () {
                setStatus(name, 'error');
                if (onError) {
                    onError();
                }
            });
        }

        function delay(callback, ms) {
            var timer = 0;
            return function () {
                var context = this, args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    callback.apply(context, args);
                }, ms || 0);
            };
        }

        $root.on('change', '.oxi-iheu-set-switch input', function () {
            var input = this;
            var value = input.checked ? input.getAttribute('data-on') : input.getAttribute('data-off');
            save(input.name, {value: value}, function () {
                input.checked = !input.checked;
            });
        });

        $root.on('change', '#oxi_image_user_permission', function () {
            save(this.name, {value: $(this).val()});
        });

        $root.on('input', '#oxi_addons_custom_parent_class', delay(function () {
            save(this.name, {name: this.name, value: $(this).val()});
        }, 1000));

        // Danger zone: "Delete all data" asks for DELETE to be typed first.
        var $dialog = $('#oxi-iheu-delete-dialog');
        var $confirm = $('#oxi-iheu-delete-confirm');
        var $submit = $('#oxi-iheu-delete-submit');
        var $dialogStatus = $dialog.find('.oxi-iheu-set-dialog-status');
        var lastFocus = null;
        var deleting = false;

        function openDialog(trigger) {
            lastFocus = trigger;
            $confirm.val('');
            $submit.prop('disabled', true);
            $dialogStatus.attr('data-state', '').text('');
            $dialog.prop('hidden', false);
            $('body').addClass('oxi-iheu-set-dialog-open');
            setTimeout(function () {
                $confirm.trigger('focus');
            }, 50);
        }

        function closeDialog() {
            if (deleting) {
                return;
            }
            $dialog.prop('hidden', true);
            $('body').removeClass('oxi-iheu-set-dialog-open');
            if (lastFocus) {
                lastFocus.focus();
            }
        }

        $root.on('click', '[data-oxi-iheu-open]', function () {
            openDialog(this);
        });

        $dialog.on('click', '[data-oxi-iheu-close]', closeDialog);

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && !$dialog.prop('hidden')) {
                closeDialog();
            }
        });

        $confirm.on('input', function () {
            $submit.prop('disabled', $confirm.val().trim() !== 'DELETE');
        });

        $confirm.on('keydown', function (e) {
            if (e.key === 'Enter' && !$submit.prop('disabled')) {
                $submit.trigger('click');
            }
        });

        $submit.on('click', function () {
            if ($confirm.val().trim() !== 'DELETE' || deleting) {
                return;
            }
            deleting = true;
            $submit.prop('disabled', true);
            $confirm.prop('disabled', true);
            $dialogStatus.attr('data-state', 'saving').text($dialogStatus.attr('data-deleting'));

            function failed() {
                deleting = false;
                $confirm.prop('disabled', false);
                $submit.prop('disabled', false);
                $dialogStatus.attr('data-state', 'error').text($dialogStatus.attr('data-error'));
            }

            $.ajax({
                url: image_hover_settings.ajaxurl,
                method: 'POST',
                data: {
                    action: 'image_hover_settings',
                    _wpnonce: image_hover_settings.nonce,
                    functionname: 'oxi_image_delete_all_data',
                    styleid: '',
                    childid: '',
                    rawdata: JSON.stringify({confirm: 'DELETE'})
                }
            }).done(function (result) {
                if (String(result).indexOf('oxi-confirmation-success') === -1) {
                    failed();
                    return;
                }
                $dialogStatus.attr('data-state', 'saved').text($dialogStatus.attr('data-done'));
                setTimeout(function () {
                    window.location.reload();
                }, 1200);
            }).fail(failed);
        });
    });
})(jQuery);
