import gql from 'graphql-tag'
import {QUESTION_FRAGMENT} from "@/graphql/faq/question-fragment";

export const CREATE_QUESTION = gql`
    mutation CreateQuestion($question: String!, $response: String!, $theme: String!, $programId: String!) {
        createQuestion(question: $question, response: $response, theme: $theme, programId: $programId) {
            ...QuestionFragment
        }
    }
    ${QUESTION_FRAGMENT}
`;
