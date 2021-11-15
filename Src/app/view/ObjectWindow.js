Ext.define('crm.view.ObjectWindow', {
    extend      : 'Ext.window.Window',
    alias       : 'widget.ObjectWindow',
    id          : "ObjectWindow",
    title       : Words_CreateObjectTitle,
    layout      : 'fit',
    resizable   : true,
//    autoScroll: true,
    minWidth    : 760,
    minHeight   : 400,
    maxHeight   : Ext.getBody().getViewSize().height - 30,
    maxWidth    : 1000,

    //TODO добавить скроллы
    closeAction : 'destroy',
    constrain   : true,
    modal       : true,
    initComponent: function() {
        Ext.apply(this, {
            id          : "ObjectWindow",
            //closeAction : 'destroy',
            items : [

                //Ext.getCmp('ObjectForm'),//xtype : 'ObjectForm'

                {
                    xtype : 'ObjectTabs'
                }
                //Ext.widget('ObjectTabs')
                /*Ext.create('Ext.tab.Panel', {
                    id      : "ObjectTabs",
                    //alias   : "widget.ObjectTabs",
                    plain   : true,
                    items   : [
                        Ext.widget('ObjectForm'),
                        ObjectPhotosTab
                        //Ext.getCmp('crm.view.ObjectPhotosTab')
                    ]
                })*/
        ]});
        this.callParent(arguments);
    },
    listeners: {
        render: {
            fn: function () {
                //alert( Ext.getBody().getViewSize().height );
            }
        }
        /*afterlayout: function() {
            var height = Ext.getBody().getViewSize().height;
            if (this.getHeight() > height) {
                //this.setHeight(height);
            }
            this.center();
        }*/
    }
});