const { registerBlockType } = wp.blocks
const { InspectorControls, RichText, PlainText } = wp.editor
const { PanelBody, SelectControl, ServerSideRender } = wp.components
const { Fragment, Component } = wp.element
const { withSelect } = wp.data;

registerBlockType('urbosa-cb/urbosa-custom', {
  title: 'Urbosa - Custom',
  icon: 'screenoptions',
  category: 'urbosa-blocks',
  keywords: [
    'Urbosa'
  ],
  edit: withSelect(select => {
    const coreEditor = select('core/editor')
    const post = coreEditor.getCurrentPost()
    return { post }
  })(class extends Component {
    constructor() {
      super(...arguments);
    }
    componentDidMount() {
      console.log('REACT CUSTOM: ', this.props);
    }
    content = () => {
      if (this.props.isSelected) {
        return <div>
          Content editing.
        </div>
      } else {
        return <ServerSideRender
          block="urbosa-cb/urbosa-custom"
          attributes={this.props.attributes}
        />
      }
    }
    render() {
      return <div>
        <InspectorControls>
          <PanelBody>
            Test
          </PanelBody>
        </InspectorControls>
        {this.content()}
      </div>
    }
  }
  ),
  save: props => {
    return null;
  }
})
