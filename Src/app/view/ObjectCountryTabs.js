Ext.define('crm.view.ObjectCountryTabs', {
//Ext.create('Ext.tab.Panel', {
    extend  : 'Ext.tab.Panel',
    id      : "ObjectCountryTabs",
    alias   : "widget.ObjectCountryTabs",
    plain   : true,
    initComponent: function() {
        Ext.apply(this, {
            id      : "ObjectCountryTabs",
            items   : [//Ext.create('Ext.Button',{text:'aha'}),
                // Здесь идет список компонентов - вкладок
                /*Ext.create('Ext.Button', {
                    iconCls   : 'ObjectsCls',
                    text      : 'ObjectsBtn',
                    id        : 'ObjectsBtn'}),
                {
                    xtype : 'ObjectForm'
                },*/
                Ext.create('crm.view.ObjectCountryForm'),
                Ext.create('crm.view.ObjectPhotosTab'),
                Ext.create('crm.view.ObjectCountryAdditionsForm')//ObjectCountryPhotosTab
                    //Ext.widget('ObjectPhotosTab')
                    //Ext.getCmp('crm.view.ObjectPhotosTab')
            ]
        });
        this.callParent(arguments);
    }
})