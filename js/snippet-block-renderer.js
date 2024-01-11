const { registerBlockType } = wp.blocks;
const { TextControl } = wp.components;

registerBlockType('qcs/snippet-block', {
    title: 'Snippet Block',
    icon: 'editor-code',
    category: 'common',
    attributes: {
        snippetId: {
            type: 'number',
        },
    },
    edit: function(props) {
        const { attributes, setAttributes } = props;
        return (
            <div>
                <TextControl
                    label="Select a Snippet"
                    value={attributes.snippetId}
                    onChange={(value) => setAttributes({ snippetId: parseInt(value) || 0 })}
                />
            </div>
        );
    },
    save: function() {
        // Server-side rendering will handle this block's output
        return null;
    },
});
