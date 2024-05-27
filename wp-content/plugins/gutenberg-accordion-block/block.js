(function(blocks, element, blockEditor, components) {
    const { createElement: el, Fragment } = element;
    const { RichText, useBlockProps, InspectorControls } = blockEditor;
    const { PanelBody, Button, IconButton } = components;

    blocks.registerBlockType('gutenberg-accordion-block/main', {
        title: 'Accordion',
        icon: 'list-view',
        category: 'layout',
        attributes: {
            items: {
                type: 'array',
                default: [
                    { title: 'Accordion Title 1', content: 'Accordion Content 1' },
                ],
                source: 'query',
                selector: '.accordion-item',
                query: {
                    title: {
                        type: 'string',
                        source: 'html',
                        selector: 'h3',
                    },
                    content: {
                        type: 'string',
                        source: 'html',
                        selector: 'div',
                    },
                },
            },
        },
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const blockProps = useBlockProps();

            const addItem = () => {
                const items = [...attributes.items];
                items.push({ title: 'New Accordion Title', content: 'New Accordion Content' });
                setAttributes({ items });
            };

            const updateItem = (index, key, value) => {
                const items = [...attributes.items];
                items[index][key] = value;
                setAttributes({ items });
            };

            const removeItem = (index) => {
                const items = [...attributes.items];
                items.splice(index, 1);
                setAttributes({ items });
            };

            return (
                el(Fragment, {},
                    el(InspectorControls, {},
                        el(PanelBody, { title: 'Accordion Settings', initialOpen: true },
                            el(Button, { isPrimary: true, onClick: addItem }, 'Add Accordion Item')
                        )
                    ),
                    el('div', blockProps,
                        attributes.items.map((item, index) => (
                            el('div', { className: 'accordion-item', key: index },
                                el(RichText, {
                                    tagName: 'h3',
                                    value: item.title,
                                    onChange: (value) => updateItem(index, 'title', value),
                                    placeholder: 'Accordion Title',
                                }),
                                el(RichText, {
                                    tagName: 'div',
                                    value: item.content,
                                    onChange: (value) => updateItem(index, 'content', value),
                                    placeholder: 'Accordion Content',
                                }),
                                el(Button, { isDestructive: true, onClick: () => removeItem(index) }, 'Remove')
                            )
                        ))
                    ),
                    el('div', {},
                        el(Button, { isPrimary: true, onClick: addItem }, 'Add Accordion Item')
                    )
                )
            );
        },
        save: function(props) {
            const { attributes } = props;
            const blockProps = useBlockProps.save();

            return (
                el('div', blockProps,
                    attributes.items.map((item, index) => (
                        el('div', { className: 'accordion-item', key: index },
                            el('h3', null, item.title),
                            el('div', null, item.content)
                        )
                    ))
                )
            );
        },
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components
);
