Ext.define('crm.view.Settings.AdPricesWindow' ,{
    extend  : 'Ext.window.Window',
    alias   : 'widget.AdPricesWindow',
    id      : "AdPricesWindow",
    title   : 'Включение выгрузки и цены за рекламу',
    autoShow: true,
    autoScroll: true,
    height  : 450,
    width   : 850,
    layout  : 'fit',
    modal       : true,
    constrain   : true,
    closeAction : 'destroy',
    initComponent: function() {
        this.items = [
            Ext.create('crm.view.Settings.AdPricesForm')
        ];
        this.callParent(arguments);
    }


});
