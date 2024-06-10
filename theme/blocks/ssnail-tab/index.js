(function () {
    const { __ } = wp.i18n; // Import translation function
    const { registerBlockType } = wp.blocks;
    const { InnerBlocks, useBlockProps } = wp.blockEditor;
    const { TextControl } = wp.components;

    registerBlockType('ossigeno/ssnail-tab', {
        edit: function (props) {
            const blockProps = useBlockProps({
                className: 'ssnail-tab-editor'
            });

            const TEMPLATE = [
                ['core/paragraph', { placeholder: 'Enter tab content' }]
            ];

            const isCurrentTab = props.attributes.panelIndex === props.context['ssnail-tabs/currentTabIndex'];
            const innerBlocksContainer = wp.element.createElement('div', { className: (isCurrentTab ? 'block' : 'hidden') }, [
                wp.element.createElement(
                    InnerBlocks, {
                    template: TEMPLATE
                }
                )
            ]);

            return wp.element.createElement('div', blockProps, [
                innerBlocksContainer
            ]);
        },
        save: function () {
            return wp.element.createElement(InnerBlocks.Content);
        }
    });
})();