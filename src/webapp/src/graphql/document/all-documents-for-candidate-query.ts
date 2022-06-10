import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from './document-fragment';

export const ALL_DOCUMENTS_FOR_CANDIDATE = gql`
    query allDocumentsForCandidate (
        $search: String, 
        $tagSearch: String, 
        $categoryId: String,
        $programId: String,
        $createdAt: String
        $offset: Int, 
        $limit: Int,
    ) {
        allDocumentsForCandidate (
            search: $search,
            tagSearch: $tagSearch,
            categoryId: $categoryId
            programId: $programId
            createdAt: $createdAt
        ) {
            items (offset: $offset, limit: $limit) {
                ...DocumentFragment
            },
            count,
        }
    }
    ${DOCUMENT_FRAGMENT}
`;
