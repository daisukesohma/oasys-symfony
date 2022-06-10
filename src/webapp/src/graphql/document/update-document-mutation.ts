import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from './document-fragment';

export const UPDATE_DOCUMENT = gql`
    mutation updateDocument (
        $id: String!,
        $name: String!,
        $fileDescriptorId: String,
        $description: String!,
        $tags: String!,
        $elaborationDate: DateTime!
        $authorId: String!,
        $visibility: String!,
        $type: String,
        $articleLink: String,
        $toBeDisplayedInHomePage: Boolean,
        $categoryId: String!
        $livrableId: String
    ) {
        updateDocument (
            id: $id,
            name: $name,
            elaborationDate: $elaborationDate,
            description: $description, 
            tags: $tags, 
            authorId: $authorId,
            fileDescriptorId: $fileDescriptorId,    
            visibility: $visibility,
            type: $type,
            articleLink: $articleLink,
            toBeDisplayedInHomePage: $toBeDisplayedInHomePage,
            categoryId: $categoryId
            livrableId: $livrableId
        ) {
            ...DocumentFragment
        }
    }
    ${DOCUMENT_FRAGMENT}
`;
