Ext.define('crm.model.ObjectsCountryGridModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id',            type: 'int'},
        {name: 'AddedDate',     type: 'string'},
        {name: 'Color',         type: 'string'},
        {name: 'ArchivedDate',  type: 'string'},
        {name: 'ImagesCount',   type: 'string'},
        {name: 'ObjectTypeName',type: 'string'},
        {name: 'DirectionName', type: 'string'},
        {name: 'Raion',         type: 'string'},
        {name: 'Distance',      type: 'int'},
        {name: 'City',          type: 'string'},
        {name: 'Street',        type: 'string'},
        {name: 'LandSquare',    type: 'string'},
        {name: 'SquareLiving',  type: 'string'},
        {name: 'Price',         type: 'string'},
        {name: 'TrfAnSiteFree', type: 'bool'},
        {name: 'TrfWinner',     type: 'bool'},
        {name: 'TrfCian',       type: 'bool'},
        {name: 'TrfCianPremium',type: 'bool'},
        {name: 'TrfAvito',      type: 'bool'},
        {name: 'TrfNavigatorFree',type:'bool'},
        {name: 'TrfRbcFree',    type: 'bool'},
        {name: 'TrfAfy',        type: 'bool'},
        {name: 'TrfYandex',     type:'bool'},
        {name: 'AdCosts',       type: 'string'},
        {name: 'Agent',         type: 'string'},

        {name: 'OwnerUserId',   type: 'string'}
    ],
    proxy       : ActiveCountryObjectsGridProxy
});