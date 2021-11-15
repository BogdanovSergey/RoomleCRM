Ext.define('crm.view.Settings.StructureTabs', {
//Ext.create('Ext.tab.Panel', {
    extend : 'Ext.tab.Panel',
    id      : "StructureTabs",
    alias   : "widget.StructureTabs",
    plain   : true,
    initComponent: function() {
        Ext.apply(this, {
            id      : "StructureTabs",
            items   : [
                // Здесь идет список компонентов - вкладок
                Ext.create('crm.view.Settings.StructurePositionsForm'),
                Ext.create('crm.view.Settings.StructureGroupsForm'),
                Ext.create('crm.view.Settings.StructureStatusesForm')
                //Ext.create('crm.view.ObjectPhotosTab'),
                //Ext.create('crm.view.ObjectAdditionsForm')
            ]
        });
        this.callParent(arguments);
    }
})
