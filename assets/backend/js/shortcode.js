jQuery.noConflict();
(function ($) {
    $(function () {
        var $root = $('.oxi-iheu-shortcodes');
        if (!$root.length) {
            return;
        }
        var $table = $root.find('.oxi-iheu-sc-table');
        var table = null;

        function request(functionname, rawdata, styleid) {
            return $.ajax({
                url: image_hover_settings.ajaxurl,
                method: 'POST',
                data: {
                    action: 'image_hover_settings',
                    _wpnonce: image_hover_settings.nonce,
                    functionname: functionname,
                    styleid: styleid,
                    childid: '',
                    rawdata: rawdata
                }
            });
        }

        // Table: newest first, search, page size and pagination.
        if ($table.length && $.fn.DataTable) {
            table = $table.DataTable({
                order: [[0, 'desc']],
                columnDefs: [{targets: [1, 2], orderable: false}],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, $root.attr('data-all')]],
                autoWidth: false,
                dom: '<"oxi-iheu-sc-toolbar"fl>t<"oxi-iheu-sc-footer"ip>',
                language: {
                    search: '',
                    searchPlaceholder: $root.attr('data-search'),
                    lengthMenu: $root.attr('data-per-page'),
                    info: $root.attr('data-info'),
                    infoEmpty: $root.attr('data-info-empty'),
                    infoFiltered: $root.attr('data-info-filtered'),
                    zeroRecords: $root.attr('data-zero'),
                    paginate: {previous: $root.attr('data-prev'), next: $root.attr('data-next')}
                }
            });
        }

        // Copy shortcode / PHP code.
        function copyText(text) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text);
            }
            return new Promise(function (resolve, reject) {
                var $tmp = $('<textarea readonly>').val(text).css({position: 'fixed', top: '-1000px'}).appendTo('body');
                $tmp[0].select();
                var ok = false;
                try {
                    ok = document.execCommand('copy');
                } catch (e) {
                    ok = false;
                }
                $tmp.remove();
                return ok ? resolve() : reject();
            });
        }

        $root.on('click', '[data-copy]', function () {
            var $btn = $(this);
            var $label = $btn.find('.oxi-iheu-sc-copy-text');
            var original = $btn.data('label') || $label.text();
            $btn.data('label', original);
            clearTimeout($btn.data('timer'));
            copyText($btn.attr('data-copy')).then(function () {
                $btn.addClass('is-copied');
                $label.text($root.attr('data-copied'));
            }, function () {
                $label.text($root.attr('data-copy-failed'));
            });
            $btn.data('timer', setTimeout(function () {
                $btn.removeClass('is-copied');
                $label.text(original);
            }, 1800));
        });

        // Dialogs.
        var lastFocus = null;
        var busy = false;

        function openDialog(id, trigger) {
            var $dialog = $('#' + id);
            lastFocus = trigger || null;
            $dialog.find('.oxi-iheu-set-dialog-status').attr('data-state', '').text('');
            $dialog.prop('hidden', false);
            $('body').addClass('oxi-iheu-set-dialog-open');
            setTimeout(function () {
                var $focus = $dialog.find('input[type=text]:visible, .is-danger-solid, .is-primary').first();
                $focus.trigger('focus');
                if ($focus.is('input[type=text]')) {
                    $focus[0].select();
                }
            }, 50);
            return $dialog;
        }

        function closeDialog() {
            if (busy) {
                return;
            }
            $('.oxi-iheu-set-dialog').prop('hidden', true);
            $('body').removeClass('oxi-iheu-set-dialog-open');
            if (lastFocus) {
                lastFocus.focus();
            }
        }

        function setStatus($dialog, state) {
            var $status = $dialog.find('.oxi-iheu-set-dialog-status');
            $status.attr('data-state', state).text(state ? $status.attr('data-' + state) : '');
        }

        $root.on('click', '[data-oxi-iheu-open]', function () {
            openDialog($(this).attr('data-oxi-iheu-open'), this);
        });
        $root.on('click', '[data-oxi-iheu-close]', closeDialog);
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $('.oxi-iheu-set-dialog:not([hidden])').length) {
                closeDialog();
            }
        });

        // Import: show the chosen file name.
        $root.on('change', '#oxi-iheu-import-dialog input[type=file]', function () {
            var $text = $(this).siblings('.oxi-iheu-sc-drop-text');
            var name = this.files && this.files.length ? this.files[0].name : $text.attr('data-empty');
            $text.text(name);
            $(this).closest('.oxi-iheu-sc-drop').toggleClass('has-file', !!(this.files && this.files.length));
        });

        // Clone.
        var $clone = $('#oxi-iheu-clone-dialog');
        $root.on('click', '.oxi-iheu-sc-clone', function () {
            var $row = $(this).closest('tr');
            var $name = $('#oxi-iheu-clone-name');
            $('#oxi-iheu-clone-id').val($row.attr('data-id'));
            $name.val($row.attr('data-name') + ' ' + $name.attr('data-suffix'));
            openDialog('oxi-iheu-clone-dialog', this);
        });

        $('#oxi-iheu-clone-form').on('submit', function (e) {
            e.preventDefault();
            var name = $.trim($('#oxi-iheu-clone-name').val());
            if (!name || busy) {
                return;
            }
            busy = true;
            setStatus($clone, 'saving');
            $clone.find('button').prop('disabled', true);
            request('layouts_clone', name, $('#oxi-iheu-clone-id').val()).done(function (result) {
                var url = $.trim(String(result));
                if (url.indexOf('http') === 0) {
                    document.location.href = url;
                    return;
                }
                busy = false;
                $clone.find('button').prop('disabled', false);
                setStatus($clone, 'error');
            }).fail(function () {
                busy = false;
                $clone.find('button').prop('disabled', false);
                setStatus($clone, 'error');
            });
        });

        // Delete.
        var $delete = $('#oxi-iheu-delete-sc-dialog');
        var $deleteRow = null;
        $root.on('click', '.oxi-iheu-sc-delete', function () {
            $deleteRow = $(this).closest('tr');
            var $title = $('#oxi-iheu-delete-sc-title');
            $title.text($title.attr('data-template').replace('%s', '“' + $deleteRow.attr('data-name') + '”'));
            $delete.find('.oxi-iheu-sc-dialog-code code').text($deleteRow.find('.oxi-iheu-sc-code code').text());
            openDialog('oxi-iheu-delete-sc-dialog', this);
        });

        $('#oxi-iheu-delete-sc-submit').on('click', function () {
            if (!$deleteRow || busy) {
                return;
            }
            busy = true;
            setStatus($delete, 'saving');
            $delete.find('button').prop('disabled', true);

            function failed() {
                busy = false;
                $delete.find('button').prop('disabled', false);
                setStatus($delete, 'error');
            }

            request('shortcode_delete', 'deleting', $deleteRow.attr('data-id')).done(function (result) {
                if ($.trim(String(result)) !== 'done') {
                    failed();
                    return;
                }
                busy = false;
                $delete.find('button').prop('disabled', false);
                lastFocus = null;
                closeDialog();
                var $count = $root.find('.oxi-iheu-sc-count');
                var left = Math.max(0, parseInt($count.text().replace(/\D/g, ''), 10) - 1);
                $count.text(left);
                if (left === 0) {
                    window.location.reload();
                    return;
                }
                $deleteRow.addClass('is-removing');
                var row = $deleteRow;
                setTimeout(function () {
                    if (table) {
                        table.row(row).remove().draw(false);
                    } else {
                        row.remove();
                    }
                }, 250);
                $deleteRow = null;
            }).fail(failed);
        });
    });
})(jQuery);
