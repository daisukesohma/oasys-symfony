import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from './document-fragment';

export const CREATE_DOCUMENT_FROM_PROGRAM = gql`
    mutation createDocumentFromProgram (
        $name: String!,
        $fileDescriptorId: String,
        $description: String!,
        $tags: String!,
        $toSign: Boolean!,
        $authorId: String!,
        $programId: String!,
        $categoryId: String!
        $hidden: Boolean,
        $elaborationDate: String,
        $type: String,
        $articleLink: String,
        $toBeDisplayedInHomePage: Boolean,
        $livrableId: String
    ) {
        createDocumentFromProgram (
            name: $name, 
            authorId: $authorId,
            description: $description, 
            toSign: $toSign,
            tags: $tags,
            fileDescriptorId: $fileDescriptorId,    
            programId: $programId,
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
