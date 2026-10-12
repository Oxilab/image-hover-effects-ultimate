jQuery.noConflict();
(function ($) {
    var styleid = '';
    var childid = '';

    async function Image_Hover_Admin_Create(functionname, rawdata, styleid, childid, callback) {
        if (functionname === "") {
            alert('Confirm Function Name');
            return false;
        }
        let result;
        try {
            result = await $.ajax({
                url: image_hover_settings.ajaxurl,
                method: 'POST',
                data: {
                    action: 'image_hover_settings',
                    _wpnonce: image_hover_settings.nonce,
                    functionname: functionname,
                    styleid: styleid,
                    childid: childid,
                    rawdata: rawdata
                }
            });
            if (result) {
                try {
                    return callback(JSON.parse(result));
                } catch (e) {
                    return callback(result)
                }
            }
            return callback('');
        } catch (error) {
            console.error(error);
            return callback('');
        }
    }

    /* Create dialog: "Use this design" asks for a name, then creates the style
       from that design's layout file and opens it in the editor. */

    $(function () {
        var $dialog = $('#oxi-iheu-create-dialog');
        if (!$dialog.length) {
            return;
        }
        var $name = $('#oxi-iheu-create-name');
        var $status = $dialog.find('.oxi-iheu-set-dialog-status');
        var lastFocus = null;
        var busy = false;

        function setStatus(state) {
            $status.attr('data-state', state).text(state ? $status.attr('data-' + state) : '');
        }

        function openDialog(trigger) {
            lastFocus = trigger;
            $('#oxi-iheu-create-source').val($(trigger).attr('data-source'));
            $dialog.find('.oxi-iheu-create-design').text($(trigger).attr('data-label'));
            $name.val('');
            setStatus('');
            $dialog.find('button').prop('disabled', false);
            $dialog.prop('hidden', false);
            $('body').addClass('oxi-iheu-set-dialog-open');
            setTimeout(function () {
                $name.trigger('focus');
            }, 50);
        }

        function closeDialog() {
            if (busy) {
                return;
            }
            $dialog.prop('hidden', true);
            $('body').removeClass('oxi-iheu-set-dialog-open');
            if (lastFocus) {
                lastFocus.focus();
            }
        }

        $(document).on('click', '.oxi-iheu-tpl-use', function (e) {
            e.preventDefault();
            openDialog(this);
        });
        $dialog.on('click', '[data-oxi-iheu-close]', closeDialog);
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && !$dialog.prop('hidden')) {
                closeDialog();
            }
        });

        // Same request as before: the name and the design's layout file path.
        $('#oxi-iheu-create-form').on('submit', function (e) {
            e.preventDefault();
            var name = $.trim($name.val());
            var source = $('#oxi-iheu-create-source').val();
            var style = source ? $('#' + source).val() : '';
            if (!name || !style || busy) {
                return;
            }
            busy = true;
            setStatus('saving');
            $dialog.find('button').prop('disabled', true);

            Image_Hover_Admin_Create('create_new', JSON.stringify({name: name, style: style}), styleid, childid, function (callback) {
                var url = $.trim(String(callback || ''));
                if (url.indexOf('http') === 0) {
                    document.location.href = url;
                    return;
                }
                busy = false;
                $dialog.find('button').prop('disabled', false);
                setStatus('error');
            });
        });
    });


    $(".oxi-addons-addons-web-template").on("click", function (e) {
        e.preventDefault();

        var functionname = "web_template";
        _This = $(this);
        _This.html('<i class="fas fa-spinner fa-pulse"></i>');
        Image_Hover_Admin_Create(functionname, _This.attr('data-effects'), _This.attr('data-value'), childid, function (callback) {
            $('#oxi-addons-style-web-template .modal-body').html(callback);
            $("#oxi-addons-style-web-template").modal("show");
            _This.html('Demos');
        });
        return false;
    });


    $(document.body).on("click", ".oxi-addons-addons-web-template-import-button", function (e) {
        e.preventDefault();
        _This = $(this);
        _This.html('<i class="fas fa-spinner fa-pulse"></i>');
        var functionname = "web_import";
        Image_Hover_Admin_Create(functionname, _This.attr('web-data'), _This.attr('web-template'), childid, function (callback) {
            setTimeout(function () {
                document.location.href = callback;
            }, 1000);
        });

    });

})(jQuery)
