import './style.scss'
import './editor.scss'

const { __ }                        = wp.i18n
const { registerBlockType }         = wp.blocks
const { InspectorControls }         = wp.editor
const { PanelBody, SelectControl  } = wp.components
const { Fragment, Component  }      = wp.element

registerBlockType( 'urbosa-cb/c', {
  title: __(' Block Name', 'Urbosa - Custom Block'),
  icon: 'dashIcon',
  category: 'common',
  keywords: [
    __('Urbosa')
  ],
  attributes: {
    test:
      {
        type: 'string',
      default: ''
    }
  },
  edit: props => {
    return (<div>Edit</div>)
  },
  save: props => {
    return (<div>Saved</div>)
  }
})