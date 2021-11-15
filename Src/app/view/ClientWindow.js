Ext.define('crm.view.ClientWindow' ,{
    extend      : 'Ext.window.Window',
    alias       : 'widget.ClientWindow',
    id          : "ClientWindow",
    title       : 'Клиенты',
    //autoShow    : true,
    autoScroll  : true,
    //resizable   : true,
    minHeight   : 450,
    minWidth    : 430,
    //layout      : 'fit',
    modal       : true,
    //constrain   : true,
    closeAction : 'destroy',
    initComponent: function() {
        //this.items = [
        Ext.apply(this, {

            //id      : 'ClientWindow',
            //itemId  : 'ClientWindow',
            border  : false,
            autoHeight: true,
            layout  : {
                type    : 'hbox',
                align   : 'stretch'
            },
            items : [
                //{
                    //xtype : 'ClientTabs'
                //}
                Ext.create('Ext.tab.Panel', {
                        width: 430,
                        //height: 480,
                        layout  : 'fit',
                        flex    : 1,
                        items: [//{title: 'Bar'}
                            Ext.create('crm.view.ClientForm')
                        ]
                    }
                    )
            ]
    } );
        //];
        this.callParent(arguments);
    }



});