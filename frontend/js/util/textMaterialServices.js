
var textMaterialServices = {
    // Get the list of materials (kept)
    getMaterialList: function() {
        // Material data
        var materials = [
            { id: 1, title: 'Material 1: Oxidation of Alcohols' },
            { id: 2, title: 'Material 2: Esterification of Alcohols' }
        ];

        // Populate the material list
        var materialList = $('#materialList');
        materials.forEach(function(material) {
            var listItem = $('<li><a href="#" class="materialItem" data-id="' + material.id + '">' + material.title + '</a></li>');
            materialList.append(listItem);
        });
    },

    // Function to load material content dynamically into #dynamicContent
    loadMaterialContent: function(id) {
        var fileName = '../static/textMaterial/textMaterial' + id + '.json';
        $.getJSON(fileName, function(data) {
            var content = '<h2><strong>' + data.title + '</strong></h2>' + data.content;
            $('#dynamicContent').html(content);
        }).fail(function(xhr, status, error) {
            console.error('Error fetching material:', error);
            $('#dynamicContent').html('<p>Error loading content. Please try again later.</p>');
        });
    }
};
