Ext.define('crm.view.ObjectTabs', {
//Ext.create('Ext.tab.Panel', {
    extend : 'Ext.tab.Panel',
    id      : "ObjectTabs",
    alias   : "widget.ObjectTabs",
    plain   : true,
    initComponent: function() {
        //console.log( Ext.getCmp('ObjectForm') );
        //console.log( Ext.widget('ObjectForm') );
        Ext.apply(this, {
            id      : "ObjectTabs",
            items   : [//Ext.create('Ext.Button',{text:'aha'}),
                // Здесь идет список компонентов - вкладок
                /*Ext.create('Ext.Button', {
                    iconCls   : 'ObjectsCls',
                    text      : 'ObjectsBtn',
                    id        : 'ObjectsBtn'}),
                {
                    xtype : 'ObjectForm'
                },*/
                Ext.create('crm.view.ObjectForm'),
                Ext.create('crm.view.ObjectPhotosTab'),
                Ext.create('crm.view.ObjectAdditionsForm'),
                Ext.create('crm.view.ObjectHistory')
                //Ext.widget('ObjectPhotosTab')
                //Ext.getCmp('crm.view.ObjectPhotosTab')
            ]
        });
        this.callParent(arguments);
    }
})