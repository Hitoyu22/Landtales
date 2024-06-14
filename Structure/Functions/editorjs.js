function initializeEditorTicket(readOnly, TicketData) {
    const editor = new EditorJS({
        onReady: () => {
            if (!readOnly) {
                new Undo({ editor });
            }
            console.log('Editor.js is ready to work!');
        },
        holder: 'editor',
        placeholder: 'Commencez à écrire...',
        tools: {
            header: {
                class: Header,
                config: {
                    placeholder: 'Entrez un titre',
                    levels: [2, 3, 4, 5],
                    defaultLevel: 2
                },
            },
            underline: Underline,
            list: {
                class: List,
                inlineToolbar: true,
                config: {
                    defaultStyle: 'unordered'
                }
            },
            warning: {
                class: Warning,
                inlineToolbar: true,
                config: {
                    titlePlaceholder: 'Titre de l\'alerte',
                    messagePlaceholder: 'Message',
                },
            },
            paragraph: {
                class: Paragraph,
                inlineToolbar: true,
            },
            strikethrough: Strikethrough,
            quote: {
                class: Quote,
                inlineToolbar: true,
                config: {
                    quotePlaceholder: 'Entrez une citation',
                    captionPlaceholder: 'Entrez les informations sur l\'auteur de la citation',
                },
            },
            delimiter: {
                class: Delimiter,
            },
            marker: {
                class: Marker,
            },
            embed: Embed,
            table: {
                class: Table,
                inlineToolbar: true,
            },
            checklist: {
                class: Checklist,
                inlineToolbar: true
            },
        },
        i18n: {
            messages: {
                "ui": {
                    "blockTunes": {
                        "toggler": {
                            "Click to tune": "Cliquez pour régler",
                            "or drag to move": "ou déplacez pour déplacer"
                        }
                    },
                    "inlineToolbar": {
                        "converter": {
                            "Convert to": "Convertir en"
                        }
                    },
                    "toolbar": {
                        "toolbox": {
                            "Add": "Ajouter",
                            "Filter": "Rechercher",
                            "Nothing found": "Rien trouvé"
                        }
                    }
                },
                "toolNames": {
                    "Text": "Texte",
                    "Heading": "Titre",
                    "List": "Liste",
                    "Warning": "Avertissement",
                    "Checklist": "Liste de contrôle",
                    "Quote": "Citation",
                    "Code": "Code",
                    "Delimiter": "Délimiteur",
                    "Table": "Tableau",
                    "Link": "Lien",
                    "Marker": "Marqueur",
                    "Bold": "Gras",
                    "Italic": "Italique",
                    "Image": "Image",
                    "Underline": "Souligner",
                    "Strikethrough": "Barrer",
                },
                "tools": {
                    "link": {
                        "Add a link": "Ajouter un lien"
                    },
                    "stub": {
                        "The block can not be displayed correctly.": "Ce bloc ne peut pas être affiché correctement."
                    },
                    "image": {
                        "Caption": "Légende",
                        "Select an Image": "Sélectionner une image",
                        "With border": "Avec bordure",
                        "Stretch image": "Étirer l'image",
                        "With background": "Avec arrière-plan"
                    },
                    "linkTool": {
                        "Link": "Entrez l'adresse du lien",
                        "Couldn't fetch the link data": "Impossible de récupérer les données du lien",
                        "Couldn't get this link data, try the other one": "Impossible d'obtenir ces données de lien, essayez l'autre",
                        "Wrong response format from the server": "Format de réponse incorrect du serveur"
                    },
                    "header": {
                        "Header": "En-tête",
                        "Heading 2": "Titre 2",
                        "Heading 3": "Titre 3",
                        "Heading 4": "Titre 4",
                        "Heading 5": "Titre 5"
                    },
                    "paragraph": {
                        "Enter something": "Entrez quelque chose"
                    },
                    "list": {
                        "Ordered": "Liste ordonnée",
                        "Unordered": "Liste non ordonnée"
                    },
                    "table": {
                        "Heading": "Titre",
                        "Add column to left": "Ajouter une colonne à gauche",
                        "Add column to right": "Ajouter une colonne à droite",
                        "Delete column": "Supprimer la colonne",
                        "Add row above": "Ajouter une ligne au-dessus",
                        "Add row below": "Ajouter une ligne en-dessous",
                        "Delete row": "Supprimer la ligne",
                        "With headings": "Avec titres",
                        "Without headings": "Sans titres"
                    },
                    "quote": {
                        "Align Left": "Aligner à gauche",
                        "Align Center": "Centrer"
                    },
                },
                "blockTunes": {
                    "delete": {
                        "Delete": "Supprimer",
                        "Click to delete": "Cliquez pour supprimer"
                    },
                    "moveUp": {
                        "Move up": "Déplacer vers le haut"
                    },
                    "moveDown": {
                        "Move down": "Déplacer vers le bas"
                    },
                    "filter": {
                        "Filter": "Filtrer"
                    }
                }
            }
        },
        data: TicketData,
        readOnly: readOnly,
        onChange: () => {
            if (!readOnly) {
                editor.save().then((savedData) => {
                    document.getElementById("editorContent").value = JSON.stringify(savedData);
                    if (savedData.blocks.length > 0) {
                        document.getElementById("submit").disabled = false;
                    }
                }).catch((error) => {
                    console.error('Saving error', error);
                });
            }
        }
    });
}

function travelEditor(readOnly, dataTravel, articleId) {
    const editor = new EditorJS({
        onReady: () => {
            if (!readOnly) {
                new Undo({ editor });
            }
            console.log('Editor.js is ready to work!');
        },
        holder: 'editor',
        placeholder: 'Commencez à écrire...',
        readOnly: readOnly,
        data: dataTravel,
        tools: {
            header: {
                class: Header,
                config: {
                    placeholder: 'Entrez un titre',
                    levels: [2, 3, 4, 5],
                    defaultLevel: 2
                },
            },
            underline: Underline,
            list: {
                class: List,
                inlineToolbar: true,
                config: {
                    defaultStyle: 'unordered'
                }
            },
            warning: {
                class: Warning,
                inlineToolbar: true,
                config: {
                    titlePlaceholder: 'Titre de l\'alerte',
                    messagePlaceholder: 'Message',
                },
            },
            paragraph: {
                class: Paragraph,
                inlineToolbar: true,
            },
            strikethrough: Strikethrough,
            quote: {
                class: Quote,
                inlineToolbar: true,
                config: {
                    quotePlaceholder: 'Entrez une citation',
                    captionPlaceholder: 'Entrez les informations sur l\'auteur de la citation',
                },
            },
            delimiter: {
                class: Delimiter,
            },
            marker: {
                class: Marker,
            },
            embed: Embed,
            table: {
                class: Table,
                inlineToolbar: true,
            },
            checklist: {
                class: Checklist,
                inlineToolbar: true
            },
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: 'https://landtales.freeddns.org/Endpoint/upload.php',
                    },
                    additionalRequestData: {
                        articleId: articleId
                    }
                }
            },
        },
        i18n: {
            messages: {
                "ui": {
                    "blockTunes": {
                        "toggler": {
                            "Click to tune": "Cliquez pour régler",
                            "or drag to move": "ou déplacez pour déplacer"
                        }
                    },
                    "inlineToolbar": {
                        "converter": {
                            "Convert to": "Convertir en"
                        }
                    },
                    "toolbar": {
                        "toolbox": {
                            "Add": "Ajouter",
                            "Filter": "Rechercher",
                            "Nothing found": "Rien trouvé"
                        }
                    }

                },
                "toolNames": {
                    "Text": "Texte",
                    "Heading": "Titre",
                    "List": "Liste",
                    "Warning": "Avertissement",
                    "Checklist": "Liste de contrôle",
                    "Quote": "Citation",
                    "Code": "Code",
                    "Delimiter": "Délimiteur",
                    "Table": "Tableau",
                    "Link": "Lien",
                    "Marker": "Marqueur",
                    "Bold": "Gras",
                    "Italic": "Italique",
                    "Image": "Image",
                    "Underline" : "Souligner",
                    "Strikethrough" : "Barrer",
                },
                "tools": {
                    "link": {
                        "Add a link": "Ajouter un lien"
                    },
                    "stub": {
                        "The block can not be displayed correctly.": "Ce bloc ne peut pas être affiché correctement."
                    },
                    "image": {
                        "Caption": "Légende",
                        "Select an Image": "Sélectionner une image",
                        "With border": "Avec bordure",
                        "Stretch image": "Étirer l'image",
                        "With background": "Avec arrière-plan"
                    },
                    "linkTool": {
                        "Link": "Entrez l'adresse du lien",
                        "Couldn't fetch the link data": "Impossible de récupérer les données du lien",
                        "Couldn't get this link data, try the other one": "Impossible d'obtenir ces données de lien, essayez l'autre",
                        "Wrong response format from the server": "Format de réponse incorrect du serveur"
                    },
                    "header": {
                        "Header": "En-tête",
                        "Heading 2": "Titre 2",
                        "Heading 3": "Titre 3",
                        "Heading 4": "Titre 4",
                        "Heading 5": "Titre 5"
                    },
                    "paragraph": {
                        "Enter something": "Entrez quelque chose"
                    },
                    "list": {
                        "Ordered": "Liste ordonnée",
                        "Unordered": "Liste non ordonnée"
                    },
                    "table": {
                        "Heading": "Titre",
                        "Add column to left": "Ajouter une colonne à gauche",
                        "Add column to right": "Ajouter une colonne à droite",
                        "Delete column": "Supprimer la colonne",
                        "Add row above": "Ajouter une ligne au-dessus",
                        "Add row below": "Ajouter une ligne en-dessous",
                        "Delete row": "Supprimer la ligne",
                        "With headings": "Avec titres",
                        "Without headings": "Sans titres"
                    },
                    "quote": {
                        "Align Left": "Aligner à gauche",
                        "Align Center": "Centrer"
                    },

                },
                "blockTunes": {
                    "delete": {
                        "Delete": "Supprimer",
                        "Click to delete": "Cliquez pour supprimer"
                    },
                    "moveUp": {
                        "Move up": "Déplacer vers le haut"
                    },
                    "moveDown": {
                        "Move down": "Déplacer vers le bas"
                    },
                    "filter": {
                        "Filter": "Filtrer"
                    }
                }
            }
        },
        onReady: () => {
            console.log('Editor.js is ready to work!')
        },
        onChange: () => {
            editor.save().then((savedData) => {
                document.getElementById("json").value = JSON.stringify(savedData)
                if (savedData.blocks.length > 0) {
                    document.getElementById("submit").disabled = false
                }
            }).catch((error) => {
                console.error('Saving error', error);
            });
        }
    });
}


function newsletterEditor(readOnly, newsletterId, newsletterData) {
    const editor = new EditorJS({
        onReady: () => {
            if (!readOnly) {
                new Undo({ editor });
            }
            console.log('Editor.js is ready to work!');
        },
        holder: 'editor',
        placeholder: 'Commencez à écrire...',
        tools: {
            header: {
                class: Header,
                config: {
                    placeholder: 'Entrez un titre',
                    levels: [2, 3, 4, 5],
                    defaultLevel: 2
                },
            },
            underline: Underline,
            list: {
                class: List,
                inlineToolbar: true,
                config: {
                    defaultStyle: 'unordered'
                }
            },
            marker: {
                class: Marker,
            },
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: 'https://landtales.freeddns.org/Endpoint/uploadNewsletter.php',
                    },
                    additionalRequestData: {
                        newsletterId: newsletterId
                    }
                }
            },
            embed: Embed,
        },
        i18n: {
            messages: {
                "ui": {
                    "blockTunes": {
                        "toggler": {
                            "Click to tune": "Cliquez pour régler",
                            "or drag to move": "ou déplacez pour déplacer"
                        }
                    },
                    "inlineToolbar": {
                        "converter": {
                            "Convert to": "Convertir en"
                        }
                    },
                    "toolbar": {
                        "toolbox": {
                            "Add": "Ajouter",
                            "Filter": "Rechercher",
                            "Nothing found": "Rien trouvé"
                        }
                    }

                },
                "toolNames": {
                    "Text": "Texte",
                    "Heading": "Titre",
                    "List": "Liste",
                    "Warning": "Avertissement",
                    "Checklist": "Liste de contrôle",
                    "Quote": "Citation",
                    "Code": "Code",
                    "Delimiter": "Délimiteur",
                    "Table": "Tableau",
                    "Link": "Lien",
                    "Marker": "Marqueur",
                    "Bold": "Gras",
                    "Italic": "Italique",
                    "Image": "Image",
                    "Underline" : "Souligner",
                    "Strikethrough" : "Barrer",
                },
                "tools": {
                    "link": {
                        "Add a link": "Ajouter un lien"
                    },
                    "stub": {
                        "The block can not be displayed correctly.": "Ce bloc ne peut pas être affiché correctement."
                    },
                    "image": {
                        "Caption": "Légende",
                        "Select an Image": "Sélectionner une image",
                        "With border": "Avec bordure",
                        "Stretch image": "Étirer l'image",
                        "With background": "Avec arrière-plan"
                    },
                    "linkTool": {
                        "Link": "Entrez l'adresse du lien",
                        "Couldn't fetch the link data": "Impossible de récupérer les données du lien",
                        "Couldn't get this link data, try the other one": "Impossible d'obtenir ces données de lien, essayez l'autre",
                        "Wrong response format from the server": "Format de réponse incorrect du serveur"
                    },
                    "header": {
                        "Header": "En-tête",
                        "Heading 2": "Titre 2",
                        "Heading 3": "Titre 3",
                        "Heading 4": "Titre 4",
                        "Heading 5": "Titre 5"
                    },
                    "paragraph": {
                        "Enter something": "Entrez quelque chose"
                    },
                    "list": {
                        "Ordered": "Liste ordonnée",
                        "Unordered": "Liste non ordonnée"
                    },
                    "table": {
                        "Heading": "Titre",
                        "Add column to left": "Ajouter une colonne à gauche",
                        "Add column to right": "Ajouter une colonne à droite",
                        "Delete column": "Supprimer la colonne",
                        "Add row above": "Ajouter une ligne au-dessus",
                        "Add row below": "Ajouter une ligne en-dessous",
                        "Delete row": "Supprimer la ligne",
                        "With headings": "Avec titres",
                        "Without headings": "Sans titres"
                    },
                    "quote": {
                        "Align Left": "Aligner à gauche",
                        "Align Center": "Centrer"
                    },

                },
                "blockTunes": {
                    "delete": {
                        "Delete": "Supprimer",
                        "Click to delete": "Cliquez pour supprimer"
                    },
                    "moveUp": {
                        "Move up": "Déplacer vers le haut"
                    },
                    "moveDown": {
                        "Move down": "Déplacer vers le bas"
                    },
                    "filter": {
                        "Filter": "Filtrer"
                    }
                }
            }
        },
        data: newsletterData,
        readOnly: readOnly,
        onChange: () => {
            if (!readOnly) {
                editor.save().then((savedData) => {
                    document.getElementById("json").value = JSON.stringify(savedData);
                    if (savedData.blocks.length > 0) {
                        document.getElementById("publish").disabled = false;
                    }
                }).catch((error) => {
                    console.error('Saving error', error);
                });
            }
        }
    });
}
