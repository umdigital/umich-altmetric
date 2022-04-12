(function(){
    const __ = wp.i18n.__;
    const _x = wp.i18n._x;
    const { useBlockProps, InnerBlocks } = wp.blockEditor;
    const { createElement } = wp.element;
    let altmetricTimer = false;

    wp.blocks.registerBlockType( 'umichaltmetric/altmetric', {
        edit: ( props ) => {
            const [ url, setURL ] = wp.element.useState( props.attributes.url );
            const [ isEditingURL, setIsEditingURL ] = wp.element.useState( ( props.attributes.url ? false : true ) );

            if( !isEditingURL ) {
                clearTimeout( altmetricTimer );

                altmetricTimer = setTimeout( _altmetric_embed_init, 2000 );
            }

            return createElement(
                'div',
                useBlockProps({
                    className: props.className
                }),
                ( !isEditingURL &&
                    createElement( wp.blockEditor.BlockControls,
                        null,
                        createElement( wp.components.ToolbarGroup,
                            null,
                            createElement( wp.components.ToolbarButton, {
                                    label    : __( 'Edit URL' ),
                                    className: 'components-toolbar__control',
                                    icon     : 'edit',
                                    onClick  : function(){
                                        setIsEditingURL( true );
                                    }
                                }
                            )
                        )
                    )
                ),
                createElement( wp.blockEditor.InspectorControls,
                    null,
                    createElement(
                        wp.components.PanelBody, {
                            title: 'Display Options',
                            initialOpen: true
                        },
                        createElement(
                            wp.components.SelectControl, {
                                label: 'Badge Type',
                                value: props.attributes.badgeType,
                                options: [{
                                    value: '',
                                    label: 'None'
                                },{
                                    value: 'badge',
                                    label: 'Badge'
                                },{
                                    value: 'donut',
                                    label: 'Small Donut'
                                },{
                                    value: 'medium-donut',
                                    label: 'Medium Donut'
                                },{
                                    value: 'large-donut',
                                    label: 'Large Donut'
                                },{
                                    value: 'bar',
                                    label: 'Small Bar'
                                },{
                                    value: 'medium-bar',
                                    label: 'Medium Bar'
                                },{
                                    value: 'large-bar',
                                    label: 'Large Bar'
                                }],
                                onChange: function( value ){
                                    props.setAttributes({
                                        badgeType: value
                                    });
                                },
                                help: createElement(
                                    wp.components.ExternalLink, {
                                        href: 'https://api.altmetric.com/embeds.html#badge-types'
                                    },
                                    __( 'Learn more about Altmetric Badges' )
                                )
                            }
                        ),
                        createElement(
                            wp.components.SelectControl, {
                                label: 'Badge Popover',
                                value: props.attributes.badgePopover,
                                options: [{
                                    value: '',
                                    label: 'None'
                                },{
                                    value: 'top',
                                    label: 'Top'
                                },{
                                    value: 'right',
                                    label: 'Right'
                                },{
                                    value: 'bottom',
                                    label: 'Bottom'
                                },{
                                    value: 'left',
                                    label: 'Left'
                                }],
                                onChange: function( value ){
                                    props.setAttributes({
                                        badgePopover: value
                                    });
                                },
                                help: createElement(
                                    wp.components.ExternalLink, {
                                        href: 'https://api.altmetric.com/embeds.html#popovers'
                                    },
                                    __( 'Learn more about Altmetric Badge Popovers' )
                                )
                            }
                        ),
                        createElement(
                            wp.components.TextControl, {
                                label: 'Items to show',
                                type:  'number',
                                value: props.attributes.limit,
                                onChange: function( value ){
                                    props.setAttributes({
                                        limit: parseInt( value )
                                    });
                                }
                            }
                        ),
                        createElement(
                            wp.components.TextControl, {
                                label: 'Custom Template',
                                type : 'text',
                                help : 'Custom template to use instead of the default one.',
                                value: props.attributes.template,
                                onChange: function( value ){
                                    props.setAttributes({
                                        template: value
                                    });
                                }
                            }
                        )
                    )
                ),
                (
                    isEditingURL &&
                    createElement(
                        wp.components.Placeholder, {
                            icon: 'admin-site-alt3',
                            label: __('Altmetric API URL'),
                            instructions: __( 'URL can be found at altmetric.com, then for the desired report go to Research Outputs Tab -> Export This Tab -> Open results in API' )
                        },
                        createElement(
                            'form', {
                                onSubmit: function( event ){
                                    if( event ) {
                                        event.preventDefault();
                                    }

                                    setIsEditingURL( false );
                                    props.setAttributes({
                                        url: url
                                    });
                                }
                            },
                            createElement(
                                'input', {
                                    type: 'url',
                                    value: url,
                                    className: 'components-placeholder__input',
                                    'aria-label': 'URL',
                                    placeholder: __( 'Enter URL to embed here…' ),
                                    onChange: function( event ) {
                                        setURL( event.target.value );
                                    }
                                }
                            ),
                            createElement(
                                'button', {
                                    type: 'submit',
                                    className: 'components-button is-primary'
                                },
                                _x( 'Embed', 'button label' )
                            )
                        ),
                        createElement(
                            'div', {
                                className: 'components-placeholder__learn-more'
                            },
                            createElement(
                                wp.components.ExternalLink, {
                                    href: 'https://help.altmetric.com/support/solutions/articles/6000241368-introduction-to-the-explorer-api'
                                },
                                __( 'Learn more about Altmetric Explorer API' )
                            )
                        )
                    )
                ),
                (
                    !isEditingURL &&
                    (
                        createElement( wp.serverSideRender, {
                            block: 'umichaltmetric/altmetric',
                            attributes: props.attributes,
                        })
                    )
                )
            )
        }
    });
}());
