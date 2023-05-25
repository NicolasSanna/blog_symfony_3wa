class DeleteArticleManager
{
    constructor()
    {
        this.deleteForms = document.querySelectorAll('.delete-form')

        for (const deleteForm of this.deleteForms)
        {
            deleteForm.addEventListener('submit', this.onDeleteSubmit.bind(this))
        }
    }

    async onDeleteSubmit(e)
    {
        e.preventDefault()

        const confirmation = window.confirm('Êtes-vous sûr de vouloir supprimer cet article ?')

        if(confirmation === true)
        {
            const url = e.currentTarget.action

            const formData = new FormData(e.currentTarget);

            const options = {
                method:'post',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body:formData
            };

            const response = await fetch(url, options)
            const articleId = await response.json();

            const row = document.getElementById(`article-${articleId}`)

            if (row != null)
            {
                row.remove();
            }
        }
    }
}

const delArtManag = new DeleteArticleManager();