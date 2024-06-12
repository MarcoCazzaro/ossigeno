(function () {
    const { useBlockProps, InspectorControls } = wp.blockEditor;
    wp.blocks.registerBlockType('ossigeno/ssnail-button', {
        edit: function (props) {
            const blockProps = useBlockProps();

            // Get the attributes
            const { label, href, target } = props.attributes;

            // Render the block
            return wp.element.createElement(
                'div',
                blockProps,
                wp.element.createElement(
                    'a',
                    {
                        "href": "#!"
                    },
                    label
                ),
                wp.element.createElement(
                    InspectorControls,
                    null,
                    wp.element.createElement(
                        wp.components.PanelBody,
                        {
                            title: 'Button Settings',
                        },
                        wp.element.createElement(
                            wp.components.TextControl,
                            {
                                label: 'Label',
                                value: label,
                                onChange: (newLabel) => props.setAttributes({ label: newLabel }),
                            }
                        ),
                        wp.element.createElement(
                            wp.components.TextControl,
                            {
                                label: 'URL',
                                value: href,
                                onChange: (newHref) => props.setAttributes({ href: newHref }),
                            }
                        ),
                        wp.element.createElement(
                            wp.components.SelectControl,
                            {
                                label: 'Target',
                                value: target,
                                options: [
                                    { label: '_self', value: '_self' },
                                    { label: '_blank', value: '_blank' },
                                ],
                                onChange: (newTarget) => props.setAttributes({ target: newTarget }),
                            }
                        ),
                    ),
                ),
            );
        },
        save: function (props) {
            const { label, href, target, style } = props.attributes;
            const { color } = style;

            return wp.element.createElement(
                'a',
                {
                    className: 'ssnail-button btn btn-primary',
                    href: href,
                    target: target,
                    style: {
                        color: color.text,
                        backgroundColor: color.background,
                    },
                    rel: "noopener",
                },
                label
            );
        },
    });
})();