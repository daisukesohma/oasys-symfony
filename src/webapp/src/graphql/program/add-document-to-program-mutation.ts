import gql from 'graphql-tag'
import {PROGRAM_FRAGMENT} from "@/graphql/program/program-fragment";

export const ADD_DOCUMENT_TO_PROGRAM = gql`
    mutation addDocumentToProgram ($programId: String!, $documentId: String!) {
        addDocumentToProgram (programId: $programId, documentId: $documentId) {
            ...ProgramFragment
        }
    }
    ${PROGRAM_FRAGMENT}
`;
