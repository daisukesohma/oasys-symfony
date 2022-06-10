import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from './document-fragment';

export const ALL_DOCUMENTS = gql`
    query allDocuments (
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
        $categoryId: String
    ) {
        allDocuments(
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
            categoryId: $categoryId
        ) {
            items(offset: $offset, limit: $limit) {
                ...DocumentFragment
            },
            count,
        }
    }
    ${DOCUMENT_FRAGMENT}
`;
