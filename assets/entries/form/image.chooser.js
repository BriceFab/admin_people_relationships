const $ = require('jquery');
import Modal from 'bootstrap/js/dist/modal';

const eaCollectionHandler = function (event) {
    const collectionImageChooserItems = $(".collection-images-chooser .field-collection-item");

    collectionImageChooserItems.each((index, item) => {
        handleInitChooser(item);
    });
}

window.addEventListener('DOMContentLoaded', eaCollectionHandler);
document.addEventListener('ea.collection.item-added', eaCollectionHandler);

function handleInitChooser(item) {
    item = $(item);

    const frameSelector = item.find('.frame_selector');
    if (frameSelector) {
        const imageSelectorId = frameSelector.attr('id');
        const frameSelectorId = `modalChooser_${imageSelectorId}`;
        const inputUrl = item.find('input[id*=url]');
        const imagePreview = item.find(`#image_${imageSelectorId}`);

        frameSelector.on('load', () => {
            frameSelector.contents().on('click', '.select', (e) => {
                const frameModalElement = document.getElementById(frameSelectorId);
                const frameModal = Modal.getInstance(frameModalElement);

                if (frameModal !== null && frameModalElement) {
                    const imageElement = $(e.target);
                    const imageFullPath = imageElement.attr('data-path');
                    const imagePath = imageFullPath.split('/').pop();

                    inputUrl.val(imagePath);
                    imagePreview.attr('src', imageFullPath);

                    const btnClose = $(frameModalElement).find('[data-bs-dismiss]');
                    if (btnClose) {
                        btnClose.trigger('click');
                    } else {
                        frameModal.hide();
                    }
                } else {
                    console.warn(`Bootstrap modal ${frameSelectorId} not found`);
                }
            });
        });
    }
}