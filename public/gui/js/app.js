(function ($) {

    var ActionCell = Backgrid.Cell.extend({
        events: {
            'click button': 'deleteRow'
        },
        deleteRow: function (e) {
            e.preventDefault();
            this.model.destroy();
        },
        render: function () {
            this.$el.html('<button>-</button>');
            return this;
        },
        className: 'borrarBoton'
    });

    var columns = [
        {
            name: "id", // The key of the model attribute
            label: "ID", // The name to display in the header
            editable: false, // By default every cell in a column is editable, but *ID* shouldn't be
            cell: Backgrid.IntegerCell.extend({orderSeparator: ''})
        },
        {
            name: "descripcion",
            label: "Descripción",
            cell: "string" // This is converted to "StringCell" and a corresponding class in the Backgrid package namespace is looked up
        },
        {
            name: "cantidad",
            label: "Cantidad",
            cell: "number" // An integer cell is a number cell that displays humanized integers
        },
        {
            name: 'borrar',
            label: 'Borrar',
            cell: ActionCell

        }
    ];


    var Gastos = Backbone.Model.extend({
        defaults: {
            "cantidad": 0,
            "descipcion": 'sin descripcion'
        },
        urlRoot: '/gastos',
        initialize: function () {
            Backbone.Model.prototype.initialize.apply(this, arguments);
            this.on("change", function (model, options) {
                if (options && options.save === false) return;
                model.save();
            });
        }
    });


    var Library = Backbone.PageableCollection.extend({
        model: Gastos,
        url: '/gastos',

        parseRecords: function (resp, options) {
            return resp._embedded.gastos;
        },

        parseState: function (resp, queryParams, state, options) {
            return {totalRecords: resp.total_items, totalPages: resp.page_count};
        },


        // Initial pagination states
        state: {
            pageSize: 10,
            sortKey: "id",
            order: 1
        },

        // You can remap the query parameters from `state` keys from
        // the default to those your server supports
        queryParams: {
            totalPages: null,
            totalRecords: null,
            sortKey: "sort",
            pageSize: 'page_size'
        }
    });

    var library = new Library();

    // Initialize a new Grid instance
    var grid = new Backgrid.Grid({
        columns: columns,
        collection: library

    });

    var paginator = new Backgrid.Extension.Paginator({

        // If you anticipate a large number of pages, you can adjust
        // the number of page handles to show. The sliding window
        // will automatically show the next set of page handles when
        // you click next at the end of a window.
        windowSize: 10, // Default is 10

        // Used to multiple windowSize to yield a number of pages to slide,
        // in the case the number is 5
        slideScale: 0.25, // Default is 0.5

        // Whether sorting should go back to the first page
        // goBackFirstOnSort: false, // Default is true

        collection: library
    });


    $('#add').on('click', function (e) {

        e.preventDefault();

        var formData = {};

        $("#addGasto").find("div").children("input").each(function (i, el) {
            if ($(el).val() !== "") {
                formData[el.id] = $(el).val();
            }
        });

        gasto = new Gastos(formData);

        gasto.save(null, {
            success: function () {
                console.log('success');
                library.add(gasto);
                $("#addGasto").find("div").children("input").each(function (i, el) {
                    this.value = '';
                });
            }
        });

    });

    // Views ////////////
    var GastosView = Backbone.View.extend({
        tagName: "div",
        className: "gastoContainer",
        template: $("#gastoTemplate").html(),

        render: function () {
            var tmpl = _.template(this.template); //tmpl is a function that takes a JSON and returns html

            this.$el.html(tmpl(this.model.toJSON())); //this.el is what we defined in tagName. use $el to get access to jQuery html() function
            return this;
        },


        events: {
            "click .delete": "deleteGasto"
        },


        deleteGasto: function () {
            //Delete model
            self = this;
            this.model.destroy({
                success: function () {
                    //Delete view
                    self.remove();
                }
            });


            return this;
        }
    });


    /************** END Gasto View */

    // var LibraryView = Backbone.View.extend({
    //     el: $("#gastos"),
    //
    //     library: library,
    //
    //     initialize: function () {
    //         this.collection = library;
    //         self = this;
    //         cf = this.collection.fetch({
    //             success: function () {
    //                 console.log(cf.models);
    //                 console.log(cf);
    //                 self.render();
    //             }
    //         });
    //         // this.render();
    //
    //         this.collection.on("add", this.renderGasto, this);
    //         this.collection.on("remove", this.removeGasto, this);
    //     },
    //
    //     render: function () {
    //         var that = this;
    //         console.log('r3ndering');
    //         _.each(this.collection.models, function (item) {
    //             that.renderGasto(item);
    //         });
    //         return this;
    //     },
    //
    //     removeGasto: function (gastoEliminado) {
    //         // var datosGastoEliminado = gastoEliminado.attributes;
    //
    //         // _.each(datosGastoEliminado, function (val, key) {
    //         //     if (datosGastoEliminado[key] === gastoEliminado.defaults[key]) {
    //         //         delete datosGastoEliminado[key];
    //         //     }
    //         // });
    //
    //         // _.each(gastos, function (gasto) {
    //         //     if (_.isEqual(gasto, datosGastoEliminado)) {
    //         //         gastos.splice(_.indexOf(gastos, gasto), 1);
    //         //     }
    //         // });
    //
    //         return this;
    //     },
    //
    //
    //     renderGasto: function (item) {
    //         var gastoView = new GastosView({
    //             model: item
    //         });
    //         this.$el.append(gastoView.render().el);
    //
    //         return this;
    //     }
    // });

    /************** END Library View */

    // var libraryView = new LibraryView();

    $("#grid").append(grid.render().el)
        .append(paginator.render().el);

    library.fetch({reset: true});


})(jQuery);