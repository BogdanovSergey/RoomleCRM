Ext.define('crm.model.ObjectsGridModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'checkbox',      type: 'bool'},
        {name: 'id',            type: 'int'},
        {name: 'AddedDate',     type: 'string'},
        {name: 'Color',         type: 'string'},
        {name: 'ArchivedDate',  type: 'string'},
        {name: 'ImagesCount',   type: 'string'},
        {name: 'ObjectTypeName',type: 'string'},
        {name: 'ObjectAgeType', type: 'string'},
        {name: 'RoomsCount',    type: 'string'},
        {name: 'City',          type: 'string'},
        {name: 'Metro',         type: 'string'},
        {name: 'Street',        type: 'string'},
        {name: 'Floors',        type: 'string'},
        {name: 'Squares',       type: 'string'},
        {name: 'Price',         type: 'string'},
        {name: 'TrfAnSiteFree',type: 'bool'},
        {name: 'TrfWinner',  type: 'bool'},
        {name: 'TrfCian',    type: 'bool'},
        {name: 'TrfCianPremium',type: 'bool'},
        {name: 'TrfAvito',   type: 'bool'},
        {name: 'TrfNavigatorFree',type:'bool'},
        {name: 'TrfRbcFree',     type:'bool'},
        {name: 'TrfAfy',     type:'bool'},
        {name: 'TrfYandex',     type:'bool'},
        {name: 'AdCosts',       type: 'string'},
        {name: 'Agent',         type: 'string'},

        {name: 'OwnerUserId',   type: 'string'}

    ],
    proxy       : ActiveCityObjectsGridProxy
});