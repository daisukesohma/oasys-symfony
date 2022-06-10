import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from './document-fragment';

export const ALL_DOCUMENTS_FOR_ADMIN = gql`
    query allDocumentsForAdmin (
        $search: String, 
        $tagSearch: String, 
        $visibility: String, 
        $sortColumn: String, 
        $sortDirection: String, 
        $avoidProgram: String, 
        $avoidEvent: String,
        $avoidHidden: Boolean,
        $type: String,
        $displayedInHomePage: Boolean,
        $offset: Int, 
        $limit: Int,
        $categoryId: String,
        $signaturePending: Boolean,
        $signedByCoach: Boolean,
        $signedByCandidate: Boolean,
        $livrableId: String,
        $programId: String
        $eventId: String,
        $createdAt: String
    ) {
        allDocumentsForAdmin (
            search: $search,
            tagSearch: $tagSearch,
            visibility: $visibility,
            sortColumn: $sortColumn,
            sortDirection: $sortDirection,
            avoidProgram: $avoidProgram,
            avoidHidden: $avoidHidden,
            avoidEvent: $avoidEvent,
            type: $type,
            displayedInHomePage: $displayedInHomePage,
            categoryId: $categoryId,
            signaturePending: $signaturePending,
            signedByCoach: $signedByCoach,
            signedByCandidate: $signedByCandidate,
            livrableId: $livrableId,
            programId: $programId
            eventId: $eventId
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
