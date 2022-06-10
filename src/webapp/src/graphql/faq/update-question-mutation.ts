import gql from 'graphql-tag'
import {QUESTION_FRAGMENT} from "@/graphql/faq/question-fragment";

export const UPDATE_QUESTION = gql`
    mutation updateQuestion (
        $id: String!
        $question: String!,
        $response: String!,
        $theme: String!,    
        $programId: String!,  
    ) {
        updateQuestion (
            id: $id,
            question: $question,
            response: $response,
            theme: $theme,
            programId: $programId,
        ) {
            ...QuestionFragment
        }
    }
    ${QUESTION_FRAGMENT}
`;
