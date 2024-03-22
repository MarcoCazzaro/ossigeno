const { registerBlockType } = wp.blocks;
const { RichText } = wp.blockEditor;
const { useBlockProps } = wp.blockEditor;

registerBlockType('snappysnail/ssnail-slider', {
    title: 'SSnail Prova per Gigi',
    icon: 'format-gallery',
    category: 'layout',
    attributes: {
        content: {
            type: 'string',
            source: 'html',
            selector: 'p',
        },
    },
    edit: function (props) {
        const blockProps = useBlockProps();
        return wp.element.createElement(RichText, {
            tagName: 'p',
            className: 'bg-red-500 p-8',
            blockProps: blockProps,
            value: props.attributes.content,
            onChange: function (content) {
                props.setAttributes({ content: content });
            },
        });
    },
    save: function (props) {
        const blockProps = useBlockProps.save();
        return wp.element.createElement(RichText.Content, {
            tagName: 'p',
            blockProps: blockProps,
            value: props.attributes.content,
        });
    },
});