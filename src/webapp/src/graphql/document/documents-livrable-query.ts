import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from './document-fragment';

export const DOCUMENTS_LIVRABLE = gql`
    query documentsLivrable (
        $search: String!
        $programId: String
        $limit: Int
    ) {
        documentsLivrable(
            search: $search,
            programId: $programId
        ) {
            items(offset: 0, limit: $limit) {
                ...DocumentFragment
            },
            count,
        }
    }
    ${DOCUMENT_FRAGMENT}
`;
