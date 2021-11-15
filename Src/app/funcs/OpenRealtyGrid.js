
function OpenRealtyGrid(RealtyType) {

    if(RealtyType == 'city') {
        Ext.getCmp('MainObjectsPanel').removeAll();
        var f = new crm.view.ObjectsGrid();
        Ext.ComponentQuery.query('#MainObjectsPanel')[0].add( f );
        f.store.setProxy( ActiveCityObjectsGridProxy ); // ставим url на активные объекты
        f.store.load();

    } else if(RealtyType == 'country') {
        //Ext.Viewport.removeAll(true);
        //Ext.getCmp('ObjectsGrid').destroy();
        Ext.getCmp('MainObjectsPanel').removeAll();
        var f = new crm.view.ObjectsCountryGrid();
        //f.render();
        Ext.ComponentQuery.query('#MainObjectsPanel')[0].add( f );//AppViewportPanel
        f.store.setProxy( ActiveCountryObjectsGridProxy ); // ставим url на активные объекты
        f.store.load();

    } else if(RealtyType == 'commerce') {
        Ext.getCmp('MainObjectsPanel').removeAll();
        var f = new crm.view.ObjectsCommerceGrid();
        //f.render();
        Ext.ComponentQuery.query('#MainObjectsPanel')[0].add( f );//AppViewportPanel
        f.store.setProxy( ActiveCommerceObjectsGridProxy ); // ставим url на активные объекты
        f.store.load();

    } else {
        alert('OpenRealtyGrid(): RealtyType:' + RealtyType + ' unknown!');
    }

}
