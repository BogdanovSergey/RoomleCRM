Ext.define('crm.view.ClientTabs', {
    extend : 'Ext.tab.Panel',
    //id      : "ClientTabs",
    alias   : "widget.ClientTabs",

    //plain   : true,
    initComponent: function() {

        Ext.apply(this, {
            //width: 400,
            //height: 400,
            id      : "ClientTabs",
            items   : [
                //Ext.create('crm.view.ClientForm')
                Ext.create('Ext.tab.Panel', {
                    width: 400,
                    height: 400,
                    items: [{
                        title: 'Bar',
                        tabConfig: {
                            title: 'Custom Title',
                            tooltip: 'A button tooltip'
                        }
                    }]
                })
            ]
        });
        this.callParent(arguments);
    }
});

