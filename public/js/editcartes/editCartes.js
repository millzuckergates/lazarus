let currentTerrain = null;
let currentTexture = null;
let currentNumTexture = 1;
let modifications = [];
const defaultStyle = {
    "left": "0",
    "top": "0",
    "width": "",
    "z-index": "",
    "position": "absolute"
}

const styleImgTexture = {
    "position": "relative",
    "width": "100px",
    "z-index": 100
}

jQuery(document).ready(function init() {
    chargerTerrains();
    chargerTextures();
    chargerPlateau(0);
});

let plateau = "";

function setBackground() {
    jQuery.ajax({
        type: "POST",
        url: "/editcartes/setBackground",
        data: {},
        success: function (data) {
            jQuery('body').css('background-image', data);
        }
    });
}

function chargerTerrains() {
    jQuery.ajax({
        type: "POST",
        url: "/editcartes/chargerTerrains",
        data: {},
        success: function (data) {
            if (data) {
                eval(data);
            }
        }
    });
}

function chargerTextures() {
    jQuery.ajax({
        type: "POST",
        url: "/editcartes/chargerTextures",
        data: {},
        success: function (data) {
            if (data) {
                eval(data);
            }
        }
    });
}

function chargerPlateau(nbDone) {
    jQuery.ajax({
        type: "POST",
        url: "/editcartes/chargerPlateauJeu",
        data: {
            nbDone: nbDone
        },
        success: function (data) {
            plateau = plateau + data;
            if (data !== "</div>") {
                chargerPlateau(nbDone + 1000)
            } else {
                chargerCSSTerrains(plateau);
            }
        }
    });
}

function chargerCSSTerrains(plateauHtml) {
    jQuery.ajax({
        type: "POST",
        url: "/editcartes/chargerCSSTerrains",
        data: {},
        success: function (data) {
            eval(data);
            afficherPlateauHTML(plateauHtml);
        }
    });
}

function afficherPlateauHTML(plateau) {
    jQuery('#plateauJeu').html(plateau);
}

function createCSSClass(selector, style) {
    let css = document.createElement('style');
    css.type = 'text/css';
    css.innerHTML = selector + "{" + style + "}";
    document.getElementsByTagName('head')[0].appendChild(css);
}

function updateCase(event) {
    $('#plateauJeu').parent().append($("#rightClickMenu").html("").css("display", "none"));
    $(".changedStyleTexture").removeClass("changedStyleTexture").children().children().css(defaultStyle);
    const coords = calculateCoords(event);

    //Reste encore la suppression de textures + prendre en compte le numéro de couche
    if (inMap(coords)) {
        let textures = getAllTexturesFromCoords(coords);
        if (textureActive()) {
            textures[currentNumTexture] = currentTexture;
        }
        let currentModification = {
            x: coords.x,
            y: coords.y,
            idTerrain: terrainActif() ? currentTerrain : getIdTerrainsFromCoords(coords),
            idTextures: JSON.stringify(textures)
        }
        addModification(currentModification);
        jQuery.ajax({
            type: "POST",
            url: "/editcartes/updateCase",
            data: currentModification,
            success: function (data) {
                if (data && data !== "") {
                    $("#x" + coords.x + "y" + coords.y).html(data);
                }
            }
        });
    }
}

function terrainActif() {
    return currentTerrain && $('#terrains').css("display") !== "none";
}

function textureActive() {
    return $('#textures').css("display") !== "none";
}

function rightClick(event) {
    const coords = calculateCoords(event);
    $(".changedStyleTexture").removeClass("changedStyleTexture").children().children().css(defaultStyle);
    if (inMap(coords)) {
        event.preventDefault();
        const caseCoord = $("#x" + coords.x + "y" + coords.y)
        caseCoord.addClass("changedStyleTexture");
        const caseCoordChild = caseCoord.children();
        const nomCase = $("<p id='nomCase'></p>").text("Case X=" + coords.x + " Y=" + coords.y)
        const rightClickMenu = $("#rightClickMenu");
        caseCoord.append(
          rightClickMenu
            .css("display", "block")
            .css("top", -120)
            .css("left", -36)
            .html(nomCase)
            .append("<div class='textureRightClick'>" + caseCoordChild.html() + "</div>")
            .append("<div id='flecheRightClickMenu'></div>")
        );
        rightClickMenu.children(".textureRightClick").children("img").css(styleImgTexture);
        return false;
    }
}

function calculateCoords(event) {
    const offsetDiv = $('#grille').offset();
    const xOffset = (xMax - xMin + 1) / 2 * LARGEUR_ISO;
    const yOffset = (yMax - yMin + 1) / 2 * HAUTEUR_ISO + HAUTEUR_ISO / 2;
    const x = event.pageX - offsetDiv.left - 24 - xOffset;
    const y = -(event.pageY - offsetDiv.top - 25 - yOffset);
    const x2 = Math.round(x / LARGEUR_ISO - y / HAUTEUR_ISO);
    const y2 = Math.round(x / LARGEUR_ISO + y / HAUTEUR_ISO);
    return {
        x: x2,
        y: y2
    }
}

function addModification(modif) {
    let add = true;
    modifications.forEach(
      (val, index) => {
          if (val.x === modif.x && val.y === modif.y) {
              modifications[index] = modif;
              add = false;
          }
      }
    )
    if (add) {
        modifications.push(modif);
    }
}

function saveCarte() {
    jQuery.ajax({
        type: "POST",
        url: "/editcartes/saveCarte",
        data: {
            modifications: JSON.stringify(modifications),
            idCarte: idCarte
        },
        success: function (data) {
            alert(data)
            if (data !== "Carte non trouvée") {
                modifications = [];
            }
        }
    });
}

function inMap(coords) {
    return coords.x >= xMin && coords.x <= xMax && coords.y >= yMin && coords.y <= yMax;
}

function displayPaletteTerrain(terrainNom) {
    const divSelected = $('#' + terrainNom + '_images');
    if (!divSelected.hasClass('selectedTerrainImages')) {
        $('.selectedTerrainImages').removeClass('selectedTerrainImages').toggle();
        divSelected.addClass('selectedTerrainImages').toggle();
        $("#arrow").css({
            "transform":"rotate(90deg)"
        })
    } else {
        divSelected.removeClass('selectedTerrainImages').toggle();
        $("#arrow").css({
        "transform":"rotate(-0deg)"
    })
    }
}

function getAllTexturesFromCoords(coords) {
    let ret = {};
    $('#x' + coords.x + 'y' + coords.y).children().children().toArray().forEach(
      (val) => {
          ret[$(val).attr('numTexture')] = $(val).attr('idTexture');
      }
    )
    return ret;
}

function getIdTerrainsFromCoords(coords) {
    return $('#x' + coords.x + 'y' + coords.y).children().attr('class').substr(2);
}

function numTextureSelect() {
    const select = $("#numTextureSelect")[0]
    select.value = Math.floor(select.value);
    if (select.value > select.max) {
        select.value = select.max;
    } else if (select.value < select.min) {
        select.value = select.min;
    }
    currentNumTexture = select.value;
}

// Boutons des couches 
function premiereCouche(){
    $('.coucheUneBtn').css('border','1px inset grey');
    $('.coucheDeuxBtn, .coucheTroisBtn, .coucheQuatreBtn, .coucheCinqBtn').css('border','none');
    currentNumTexture = 1;
}
function deuxiemeCouche(){
    $('.coucheDeuxBtn').css('border','1px inset grey');
    $('.coucheUneBtn, .coucheTroisBtn, .coucheQuatreBtn, .coucheCinqBtn').css('border','none');
    currentNumTexture = 2;
}
function troisiemeCouche(){
    $('.coucheTroisBtn').css('border','1px inset grey');
    $('.coucheDeuxBtn, .coucheUneBtn, .coucheQuatreBtn, .coucheCinqBtn').css('border','none');
    currentNumTexture = 3;
}
function quatriemeCouche(){
    $('.coucheQuatreBtn').css('border','1px inset grey');
    $('.coucheDeuxBtn, .coucheTroisBtn, .coucheUneBtn, .coucheCinqBtn').css('border','none');
    currentNumTexture = 4;
}
function cinquiemeCouche(){
    $('.coucheCinqBtn').css('border','1px inset grey');
    $('.coucheDeuxBtn, .coucheTroisBtn, .coucheQuatreBtn, .coucheUneBtn').css('border','none');
    currentNumTexture = 5;
}









