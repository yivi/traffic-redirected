jQuery(function ($) {

    var unir_date_formatter = _.extend({}, Backgrid.CellFormatter.prototype, {
        fromRaw: function (rawValue, model) {

            if (!rawValue) {
                return '';
            }

            var months = [
                '',
                'Ene',
                'Feb',
                'Mar',
                'Abr',
                'May',
                'Jun',
                'Jul',
                'Ago',
                'Sep',
                'Oct',
                'Nov',
                'Dic'
            ];

            var matches = rawValue.match(/2\d(\d\d)-(\d\d)-(\d\d) (\d\d:\d\d:\d\d)/);

            return matches[3] + ' ' + months[matches[2]] + ' ' + matches[1] + ' (' + matches[4] + ')';
        }
    });

    var Unir_DateTime_Cell = Backgrid.StringCell.extend({
        formatter: unir_date_formatter,
        className: 'timestamp'
    });

    var BorrarCell = Backgrid.Cell.extend({
        events: {
            'click button': 'deleteRow'
        },
        deleteRow: function (e) {
            e.preventDefault();
            this.model.destroy();
        },
        render: function () {
            this.$el.html('<button>x</button>');
            return this;
        },
        className: 'borrarBoton'
    });

    var columns = [
        {
            name: "id", // The key of the model attribute
            label: "ID", // The name to display in the header
            editable: false,
            renderable: false,
            cell: Backgrid.IntegerCell.extend({orderSeparator: ''})
        },
        {
            name: "origin",
            label: "Origen",
            cell: "uri" // This is converted to "StringCell" and a corresponding class in the Backgrid package namespace is looked up
        },
        {
            name: "target",
            label: "Destino",
            cell: "uri" // An integer cell is a number cell that displays humanized integers
        },
        {
            name: "redirect_type",
            label: "Tipo de Redirección",
            cell: Backgrid.SelectCell.extend({
                optionValues: [["Idéntico", "1"], ["Comienza por", "2"], ["Comienza + Captura", "3"]]
            })
        },
        {
            name: "created_at",
            label: "Creado",
            cell: Unir_DateTime_Cell,
            editable: false
        },
        {
            name: "modified_at",
            label: 'Modificado',
            cell: Unir_DateTime_Cell,
            editable: false
        },
        {
            name: 'borrar',
            label: 'Borrar',
            cell: BorrarCell

        }
    ];

    var loadingOn = function (event) {
        $("body").prepend($("<div id='loading-spinner'>"));
    };

    var loadingOff = function (event) {
        $('#loading-spinner').remove();
    };

    var insert_error = function (event, resp_object) {
        message = "Falló la validación por:\n";
        $.each(resp_object.responseJSON.validation_messages, function (k, v) {
            $.each(v, function (k2, v2) {
                message += "<strong>(" + k + ")</strong>: " + v2 + ', ';
            });
        });

        alert_me(message);
    };

    var alert_me = function (message) {
        $alert = $('<div>');
        $alert.html('<span>' + message + '</span>');
        $alert.addClass('alert').addClass('alert-warning');
        $close = $('<a>').attr('href', '#').attr('data-dismiss', 'alert').attr('aria-label', 'close').addClass('close').html('&times;');
        $alert.append($close);
        $alert.appendTo($('#errorlog'));
        loadingOff();
    };


    var Redirects = Backbone.Model.extend({
        urlRoot: '/global/redirects',
        initialize: function () {
            Backbone.Model.prototype.initialize.apply(this, arguments);

            this.on("change", function (model, options) {
                if (options && options.save === false) return;
                model.save(null, {wait: true});
            });

            this.on('fetch request', function (e) {
                loadingOn(e);
            });

            this.on('sync error', function (e) {
                loadingOff(e);
            });

            this.on('error', function (model, resp) {
                insert_error(model, resp);
            });
        }
    });

    var RedirectsCollection = Backbone.PageableCollection.extend({
        initialize: function () {
            this.on('fetch', function (e) {
                loadingOn(e);
            });
            this.on('sync', function (e) {
                loadingOff(e);
            });
        },

        model: Redirects,
        url: '/global/redirects',

        parseRecords: function (resp, options) {
            return resp._embedded.items;
        },

        parseState: function (resp, queryParams, state, options) {
            return {totalRecords: resp.total_items, totalPages: resp.page_count};
        },

        // Initial pagination states
        state: {
            pageSize: 50,
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

    var redirects_collection = new RedirectsCollection();

    // Initialize a new Grid instance
    var grid = new Backgrid.Grid({
        columns: columns,
        collection: redirects_collection,
        className: 'table table-stripped backgrid'

    });

    var paginator = new Backgrid.Extension.Paginator({

        windowSize: 10, // Default is 10
        slideScale: 0.25, // Default is 0.5
        collection: redirects_collection
    });


    $('#add').on('click', function (e) {

        e.preventDefault();

        var formData = {};

        $("#nueva_redireccion").find("input,select").each(function (i, el) {
            if ($(el).val() !== "") {
                formData[el.id] = $(el).val();
            }
        });

        var redirect = new Redirects(formData);

        redirect.save(null, {
            success: function () {
                redirects_collection.unshift(redirect);
                $("#nueva_redireccion").find("input,select").each(function (i, el) {
                    this.value = '';
                });
            }
        });
    });


    $("#grid").append(grid.render().el)
        .append(paginator.render().el);

    var originFilter = new Backgrid.Extension.ServerSideFilter({
        collection: redirects_collection,
        // the name of the URL query parameter
        name: "origin",
        placeholder: "Filtrar por origen" // HTML5 placeholder for the search box
    });

    var targetFilter = new Backgrid.Extension.ServerSideFilter({
        collection: redirects_collection,
        // the name of the URL query parameter
        name: "target",
        placeholder: "Filtrar por destino" // HTML5 placeholder for the search box
    });

    $("#redireccion_filtros").append(originFilter.render().el).append(targetFilter.render().el);

    // Patch Model and Collection.
    _.each(["Model", "Collection"], function (name) {
        // Cache Backbone constructor.
        var ctor = Backbone[name];
        // Cache original fetch.
        var fetch = ctor.prototype.fetch;

        // Override the fetch method to emit a fetch event.
        ctor.prototype.fetch = function () {
            // Trigger the fetch event on the instance.
            this.trigger("fetch", this);

            // Pass through to original fetch.
            return fetch.apply(this, arguments);
        };
    });

    redirects_collection.fetch({reset: true});

    $('#open_importer').click(function (e) {
        $('#uploader_div').show();
    });

    $('#uploader_div').find('.closer').click(function (e) {
        $('#uploader_div').hide();
    });

    $('#uploader_form').submit(function (e) {
        e.preventDefault();

        // Note: if you observe 422 responses, check what's assembled into fd amd
        // that it looks correct.
        var fd = new FormData(jQuery(this)[0]);

        waitingDialog.show('Procesando...');

        jQuery.ajax({
            url: '/global/redirects/collections', // Specify the path to your API service
            type: 'POST',              // Assuming creation of an entity
            contentType: false,        // To force multipart/form-data
            data: fd,
            processData: false,
            success: function (data) {
                // Handle the response on success
                waitingDialog.hide();
                $('#uploader_div').hide();
                console.log(JSON.stringify(data));
                if (data.fallos > 0) {
                    var message = data.fallos + ' registros no se pudieron importar. Los registros que han fallado se pueden descargar desde aquí: ';
                    message += '<a href="/global/redirects/collection/import_log/' + data.report + '">' + data.report + '</a>';
                    alert_me(message);
                }

            }
        });
    });

});

