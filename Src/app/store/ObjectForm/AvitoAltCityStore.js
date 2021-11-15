Ext.define('crm.store.ObjectForm.AvitoAltCityStore', {  //#AvitoAltAddr
    extend      : 'Ext.data.Store',
    storeId     : 'AvitoAltCityStore',
    requires    : 'crm.model.KladrModel',
    model       : 'crm.model.KladrModel',
    proxy: {
        type    : 'ajax',
        url     : MainSiteUrl + 'Super.php?Action=GetAvitoCitiesArrByRegion',
        reader  : 'json',
        extraParams   : {
            ChosenRegion : '',
            ChosenCity   : ''
        }
    }
    //,autoLoad: true
});