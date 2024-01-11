(function() {
    tinymce.PluginManager.add('qcs_button', function(editor, url) {
        editor.addButton('qcs_button', {
            text: 'Insert Snippet',
            icon: false,
            onclick: function() {
                // Fetch snippet options via AJAX
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        action: 'qcs_get_snippet_options',
                    },
                    success: function(response) {
                        if (response && response.length > 0) {
                            editor.windowManager.open({
                                title: 'Insert Snippet',
                                body: [
                                    {
                                        type: 'listbox',
                                        name: 'snippet',
                                        label: 'Select a Snippet',
                                        values: response,
                                    }
                                ],
                                onsubmit: function(e) {
                                    editor.insertContent('[qcs_snippet id="' + e.data.snippet + '"]');
                                }
                            });
                        } else {
                            alert('No snippets available.'); // Display an alert for no snippets
                        }
                    },
                });
            }
        });
    });
})();
