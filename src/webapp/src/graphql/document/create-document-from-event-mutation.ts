import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from './document-fragment';

export const CREATE_DOCUMENT_FROM_EVENT = gql`
    mutation createDocumentFromEvent (
        $name: String!,
        $fileDescriptorId: String,
        $description: String!,
        $tags: String!,
        $toSign: Boolean!,
        $authorId: String!,
        $eventId: String!,
        $categoryId: String!
        $hidden: Boolean,
        $elaborationDate: String,
        $type: String,
        $articleLink: String,
        $toBeDisplayedInHomePage: Boolean,
        $livrableId: String
    ) {
        createDocumentFromEvent (
            name: $name, 
            authorId: $authorId,
            description: $description, 
            tags: $tags,
            toSign: $toSign,
            fileDescriptorId: $fileDescriptorId,    
            eventId: $eventId,
            hidden: $hidden,
            elaborationDate: $elaborationDate,
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
