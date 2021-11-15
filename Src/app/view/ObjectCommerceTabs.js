Ext.define('crm.view.ObjectCommerceTabs', {
//Ext.create('Ext.tab.Panel', {
    extend  : 'Ext.tab.Panel',
    id      : "ObjectCommerceTabs",
    alias   : "widget.ObjectCommerceTabs",
    plain   : true,
    initComponent: function() {
        Ext.apply(this, {
            id      : "ObjectCommerceTabs",
            items   : [//Ext.create('Ext.Button',{text:'aha'}),
                // Здесь идет список компонентов - вкладок
                /*Ext.create('Ext.Button', {
                    iconCls   : 'ObjectsCls',
                    text      : 'ObjectsBtn',
                    id        : 'ObjectsBtn'}),
                {
                    xtype : 'ObjectForm'
                },*/
                Ext.create('crm.view.ObjectCommerceForm'),
                Ext.create('crm.view.ObjectCommercePhotosTab'),
                Ext.create('crm.view.ObjectCommerceAdditionsForm')//ObjectCommercePhotosTab
                    //Ext.widget('ObjectPhotosTab')
                    //Ext.getCmp('crm.view.ObjectPhotosTab')
            ]
        });
        this.callParent(arguments);
    }
})