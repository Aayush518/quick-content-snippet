const { registerBlockType } = wp.blocks;
const { Button, Modal } = wp.components;

registerBlockType('qcs/snippet-button-block', {
    title: 'Snippet Button Block',
    icon: 'editor-code',
    category: 'common',
    edit: function(props) {
        const { insertBlock } = wp.blocks;
        const { Button, Modal } = wp.components;

        const openModal = () => {
            const modalContent = (
                <div>
                    <h3>Select a Snippet</h3>
                    <select
                        onChange={(event) => {
                            const snippetId = parseInt(event.target.value);
                            if (snippetId > 0) {
                                insertBlock('qcs/snippet-block', { snippetId });
                                closeModal();
                            }
                        }}
                    >
                        <option value="0">Select a Snippet</option>
                        {/* Fetch snippet options dynamically */}
                    </select>
                </div>
            );

            const modal = (
                <Modal title="Insert Snippet" onRequestClose={closeModal}>
                    {modalContent}
                </Modal>
            );

            wp.data.dispatch('core/edit-post').openModal({
                content: modal,
            });
        };

        const closeModal = () => {
            wp.data.dispatch('core/edit-post').closeModal();
        };

        return (
            <div>
                <Button onClick={openModal}>Insert Snippet</Button>
            </div>
        );
    },
    save: function() {
        // Server-side rendering will handle this block's output
        return null;
    },
});
