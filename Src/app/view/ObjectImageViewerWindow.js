Ext.define('crm.view.ObjectImageViewerWindow' ,{
    extend  : 'Ext.window.Window',
    alias   : 'widget.ObjectImageViewerWindow',
    id      : "ObjectImageViewerWindow",
    title   : 'Фотография объекта',
    autoShow:  false,
    autoScroll: true,
    height  : 500,
    width   : 700,
    maximizable: true,
    //layout  : 'fit',
    /*layout : {
        type : 'vbox',
        align : 'center'
    },*/
    modal   : true,
    closeAction: 'destroy',
    html    : " "
    /*onRender: function(ct,pos) {
        //Call superclass
        this.callParent(arguments);
        if (this.getHeight() > this.maxHeight) {
            this.setHeight(this.maxHeight);
        }
        this.center();
    }*/

});
/*.on('afterrender', function() {
    if (this.getHeight() > this.maxHeight) {
        this.setHeight(this.maxHeight);
    }
    this.center();
}, this);*/
