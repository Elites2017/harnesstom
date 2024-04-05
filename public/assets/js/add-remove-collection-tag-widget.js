// btn delete the tag / collection added
const addTagFormDeleteLink = (item) => {
  const removeFormButton = document.createElement("button");
  // jquery code, but we can also use simple javascript code to select the element (btn)
  $(removeFormButton).addClass("btn btn-outline-danger mb-1 mt-1");
  removeFormButton.innerText = "Delete";

  item.append(removeFormButton);

  removeFormButton.addEventListener("click", (e) => {
    e.preventDefault();
    // remove the li for the tag form
    item.remove();
  });
};

const addFormToCollection = (e) => {
  const collectionHolder = document.querySelector("." + e.currentTarget.dataset.collectionHolderClass);

  const item = document.createElement("li");

  item.innerHTML = collectionHolder.dataset.prototype.replace(
    /__name__/g,
    collectionHolder.dataset.index
  );

  collectionHolder.appendChild(item);

  collectionHolder.dataset.index++;

  // add a delete link to the new form
  addTagFormDeleteLink(item);
};

document.querySelectorAll(".add_item_link").forEach((btn) => {
  btn.addEventListener("click", addFormToCollection);
});

document.querySelectorAll("ul.tags li").forEach((tag) => {
  addTagFormDeleteLink(tag);
});
