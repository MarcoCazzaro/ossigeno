(function () {
    const { useBlockProps, InnerBlocks } = wp.blockEditor;
    wp.blocks.registerBlockType('ossigeno/ssnail-container', {
        edit: function (props) {
            const blockProps = useBlockProps({
                style: {
                    outline: '2px solid var(--wp--preset--color--primary)',
                    padding: '1rem',
                    "max-width": '40rem',
                    margin: '0 auto'
                }
            });

            return wp.element.createElement(
                'div',
                blockProps,
                wp.element.createElement(InnerBlocks)
            );
        },
        save: function (props) {
            const blockProps = useBlockProps.save({
                className: 'ssnail-container'
            });

            return wp.element.createElement(
                'div',
                blockProps,
                wp.element.createElement(InnerBlocks.Content)
            );
        },
    });
})();