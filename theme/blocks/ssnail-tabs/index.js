(function () {
    const { __ } = wp.i18n; // Import translation function
    const { registerBlockType } = wp.blocks;
    const { InnerBlocks, useBlockProps } = wp.blockEditor;

    registerBlockType('ossigeno/ssnail-tabs', {
        edit: function (props) {
            const blockProps = useBlockProps({
                className: 'ssnail-tabs-editor'
            });
            const { clientId } = props;

            const handleTabLabelChange = (index, value) => {
                const tabLabels = [...props.attributes.tabLabels];
                tabLabels[index] = value;
                props.setAttributes({ tabLabels });
            };

            const handleAddTab = () => {
                // REF: https://github.com/WordPress/gutenberg/issues/15893
                const tabLabels = [...props.attributes.tabLabels, 'Tab ' + (props.attributes.tabLabels.length + 1)];
                props.setAttributes({ tabLabels });
                const blockToInsert = wp.blocks.createBlock('ossigeno/ssnail-tab', { parentId: clientId, panelIndex: (tabLabels.length - 1), placeholder: 'Enter tab content' }, []);
                wp.data.dispatch('core/block-editor').insertBlock(blockToInsert, tabLabels.length - 1, clientId);
                props.setAttributes({ currentTabIndex: tabLabels.length - 1 });
            };

            const handleRemoveTab = (index) => {
                // REF: https://github.com/WordPress/gutenberg/issues/15893
                const tabLabels = [...props.attributes.tabLabels];
                if (tabLabels.length === 1) {
                    return;
                }
                const innerBlocks = wp.data.select('core/block-editor').getBlocksByClientId(clientId)[0].innerBlocks;
                wp.data.dispatch('core/block-editor').removeBlock(innerBlocks[index].clientId);
                tabLabels.splice(index, 1);
                props.setAttributes({ tabLabels });
                props.setAttributes({ currentTabIndex: 0 });
            };

            const tabLabelsContainer = wp.element.createElement('div', { className: 'ssnail-labels-wrapper' },
                props.attributes.tabLabels.map((label, index) => {
                    return wp.element.createElement('div', {
                        className: 'ssnail-label' + (index === props.attributes.currentTabIndex ? ' active' : ''),
                    }, [
                        wp.element.createElement('input', {
                            type: 'text',
                            value: label,
                            onChange: (event) => handleTabLabelChange(index, event.target.value),
                            onClick: function (event) {
                                /* When clicking on a tab input, update the currentTabIndex */
                                props.setAttributes({ currentTabIndex: index });
                            }
                        }),
                        wp.element.createElement('button', { onClick: () => handleRemoveTab(index) }, '-'),
                    ]);
                }),
                wp.element.createElement('button', { onClick: handleAddTab }, 'Add Tab')
            );

            const TEMPLATE = [
                ['ossigeno/ssnail-tab', { parentId: clientId, panelIndex: 0 }]
            ];

            return wp.element.createElement('div', blockProps, [
                tabLabelsContainer,
                wp.element.createElement('div', { className: 'ssnail-tabs-wrapper' }, [
                    wp.element.createElement(
                        InnerBlocks,
                        {
                            allowedBlocks: ['ossigeno/ssnail-tab'],
                            template: TEMPLATE,
                            renderAppender: false
                        }
                    )
                ])
            ]);
        },
        save: function () {
            return wp.element.createElement(InnerBlocks.Content);
        }
    });
})();