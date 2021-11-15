Ext.define('crm.view.ObjectCountryDataView', {
//DataView = Ext.create('Ext.view.View', {
    //id: "ImagesDataView",
    extend  : 'Ext.view.View',
    id          : "ObjectDataView",
    alias   : 'widget.ObjectDataView',
    autoScroll: true,
    initComponent: function() {
        Ext.apply(this, {
            id          : "ObjectDataView",
            store   : 'ObjectDataViewStore',
            multiSelect : true,
            height      : 400,
            border      : false,
            trackOver   : true,
            shrinkWrap  : 2,
            overItemCls : 'x-item-over',
            itemSelector: 'div.thumb-wrap',
            emptyText   : 'Нет загруженных фотографий',
         
        tpl: [

            '<tpl for=".">',
            '<div class="thumb-wrap">',
                '<table style="border: 1px solid #99BCE8;float:left;width:120px;height:120px;margin-right: 10px;margin-bottom: 10px;">',
                    '<tr>',
                        '<td>',
                        '<div class="button-action" style="background:url({url});background-repeat:no-repeat;width:110px;height:110px;">',
                        '<a href="#" onclick="DeleteUploadedImage({id:htmlEncode})"><img src="icons/cross.png"></a>',
                        '</td>',
                        '</div>',
                    '</tr>',
                '</table>',
            '</div>',
            '</tpl>'

                /*'<tpl for=".">',
                '<div class="thumb-wrap" id="{name:stripTags}">',
                '<div class="thumb" style="float:left;height:100px;width:150px;"><img src="{url}" title="{name:htmlEncode}" style="" class="button-action"> <a href="#" onclick="DeleteUploadedImage({id:htmlEncode})"><img src="icons/cross.png"><!--{shortName:htmlEncode}--></a></div>',
                '</div>',
                '</tpl>'*/
            ]
            /*plugins: [
                Ext.create('Ext.ux.DataView.DragSelector', {}),
                Ext.create('Ext.ux.DataView.LabelEditor', {dataIndex: 'name'})
            ]*/
            /*plugins: [
             Ext.create('Ext.ux.DataView.DragSelector', {})
             ],*/

        } );
        this.callParent(arguments);
    },

    prepareData: function(data) {
        Ext.apply(data, {
            shortName: Ext.util.Format.ellipsis(data.name, 25),
            sizeString: Ext.util.Format.fileSize(data.size),
            dateString: Ext.util.Format.date(data.lastmod, "m/d/Y g:i a")
        });
        return data;
    },
    listeners: {
        itemmousedown: function (me, record, item, index, e) {
            // клик на картинку
            var ImgId = record.get('id');
            var className = e.target.className;
            if (className == "button-action") {
                //alert("Clicked action on item: " + ImgId);
                var w = Ext.widget('ObjectImageViewerWindow');
                //w.update('left <img src="' + LoadObjectImageUrl + ImgId + '"> right');
                w.update('<img src="' + LoadObjectImageUrl + ImgId + '">');
                w.show();
            }
        }
    }
});