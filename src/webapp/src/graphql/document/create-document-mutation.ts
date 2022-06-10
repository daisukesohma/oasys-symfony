import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from './document-fragment';

export const CREATE_DOCUMENT = gql`
    mutation createDocument (
        $name: String!,
        $fileDescriptorId: String,
        $description: String!,
        $tags: String!,
        $elaborationDate: String!,
        $authorId: String!,
        $visibility: String!,
        $toSign: Boolean!,
        $categoryId: String!
        $type: String,
        $articleLink: String,
        $toBeDisplayedInHomePage: Boolean,
        $livrableId: String
    ) {
        createDocument (
            name: $name, 
            authorId: $authorId,
            description: $description, 
            tags: $tags,
            toSign: $toSign,
            elaborationDate: $elaborationDate,
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
