import gql from 'graphql-tag'
import {QUESTION_FRAGMENT} from "@/graphql/faq/question-fragment";

export const ALL_QUESTIONS = gql`
    query allQuestions ($search: String, $theme: String, $offset: Int, $limit: Int, $sortColumn: String, $sortDirection: String) {
        allQuestions(search: $search, theme: $theme, sortColumn: $sortColumn, sortDirection: $sortDirection) {
            items(offset: $offset, limit: $limit) {
                ...QuestionFragment
            },
            count,
        }
    }
    ${QUESTION_FRAGMENT}
`;
