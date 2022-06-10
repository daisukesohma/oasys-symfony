import gql from 'graphql-tag'

export const ALL_DOCUMENT_CATEGORIES = gql`
    query allDocumentCategories (
        $search: String,
        $tagSearch: String,
        $visibility: String,
        $signaturePending: Boolean,
        $signedByCoach: Boolean,
        $signedByCandidate: Boolean,
        $livrableId: String,
        $programId: String,
        $eventId: String,
        $createdAt: String
    ) {
        allDocumentCategories {
            items {
                id
                label
                documents (
                    search: $search
                    tagSearch: $tagSearch
                    visibility: $visibility
                    signaturePending: $signaturePending
                    signedByCoach: $signedByCoach
                    signedByCandidate: $signedByCandidate
                    livrableId: $livrableId
                    programId: $programId
                    eventId: $eventId
                    createdAt: $createdAt
                ) {
                    count
                }
            }
        }
    }
`;
